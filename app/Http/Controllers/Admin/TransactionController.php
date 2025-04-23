<?php

// app/Http/Controllers/Admin/TransactionController.php
namespace App\Http\Controllers\Admin;
    
use App\Http\Controllers\Controller;
use App\Models\UserTransaction;
use App\Models\MembershipTransaction;
use App\Services\GameApiService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    /**
     * Display a listing of the user transactions
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $transactions = UserTransaction::with(['user', 'reseller.user', 'game', 'service']);
                
            return DataTables::of($transactions)
                ->addColumn('invoice', function($transaction) {
                    return '<a href="' . route('admin.transactions.show', $transaction->id) . '">' . 
                        $transaction->invoice_number . '</a>';
                })
                ->addColumn('customer', function($transaction) {
                    return $transaction->user->name;
                })
                ->addColumn('reseller', function($transaction) {
                    return $transaction->reseller->store_name;
                })
                ->addColumn('service', function($transaction) {
                    return $transaction->game->name . ' - ' . $transaction->service->name;
                })
                ->addColumn('amount_formatted', function($transaction) {
                    return 'Rp ' . number_format($transaction->amount, 0, ',', '.');
                })
                ->addColumn('payment_status_badge', function($transaction) {
                    if ($transaction->payment_status === 'paid') {
                        return '<span class="badge bg-success">Paid</span>';
                    } elseif ($transaction->payment_status === 'pending') {
                        return '<span class="badge bg-warning">Pending</span>';
                    } elseif ($transaction->payment_status === 'expired') {
                        return '<span class="badge bg-danger">Expired</span>';
                    } else {
                        return '<span class="badge bg-danger">Failed</span>';
                    }
                })
                ->addColumn('process_status_badge', function($transaction) {
                    if ($transaction->process_status === 'completed') {
                        return '<span class="badge bg-success">Completed</span>';
                    } elseif ($transaction->process_status === 'processing') {
                        return '<span class="badge bg-info">Processing</span>';
                    } elseif ($transaction->process_status === 'pending') {
                        return '<span class="badge bg-warning">Pending</span>';
                    } else {
                        return '<span class="badge bg-danger">Failed</span>';
                    }
                })
                ->addColumn('date', function($transaction) {
                    return $transaction->created_at->format('d M Y H:i');
                })
                ->addColumn('actions', function($transaction) {
                    $actions = '<a href="' . route('admin.transactions.show', $transaction->id) . '" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i>
                    </a>';
                    
                    if ($transaction->payment_status === 'paid' && $transaction->process_status !== 'completed') {
                        $actions .= ' <button type="button" data-id="' . $transaction->id . '" class="btn btn-sm btn-primary btn-process">
                            <i class="fas fa-sync-alt"></i> Process
                        </button>';
                    }
                    
                    return $actions;
                })
                ->rawColumns(['invoice', 'payment_status_badge', 'process_status_badge', 'actions'])
                ->make(true);
        }
        
        return view('admin.transactions.index');
    }
    
    /**
     * Display the specified transaction
     */
    public function show($id)
    {
        $transaction = UserTransaction::with([
            'user', 
            'reseller.user', 
            'game', 
            'service',
            'option'
        ])->findOrFail($id);
        
        return view('admin.transactions.show', compact('transaction'));
    }
    
    /**
     * Process the transaction manually
     */
    public function process($id)
    {
        $transaction = UserTransaction::findOrFail($id);
        
        if ($transaction->payment_status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi belum dibayar!'
            ]);
        }
        
        if ($transaction->process_status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi sudah selesai diproses!'
            ]);
        }
        
        try {
            // Process transaction via service
            app(GameApiService::class)->processTransaction($transaction);
            
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Update transaction status manually
     */
    public function updateStatus(Request $request, $id)
    {
        $transaction = UserTransaction::findOrFail($id);
        
        $request->validate([
            'process_status' => 'required|in:pending,processing,completed,failed',
        ]);
        
        // Update status
        $transaction->update([
            'process_status' => $request->process_status,
            'completed_at' => $request->process_status === 'completed' ? now() : null,
        ]);
        
        // Send notification
        if ($request->process_status === 'completed') {
            event(new \App\Events\TransactionCompleted($transaction));
        } elseif ($request->process_status === 'failed') {
            event(new \App\Events\TransactionFailed($transaction));
        }
        
        return redirect()->route('admin.transactions.show', $id)
            ->with('success', 'Status transaksi berhasil diperbarui!');
    }
    
    /**
     * Display a listing of membership transactions
     */
    public function membershipIndex(Request $request)
    {
        if ($request->ajax()) {
            $transactions = MembershipTransaction::with(['reseller.user', 'package']);
                
            return DataTables::of($transactions)
                ->addColumn('invoice', function($transaction) {
                    return '<a href="' . route('admin.transactions.membership.show', $transaction->id) . '">' . 
                        $transaction->invoice_number . '</a>';
                })
                ->addColumn('reseller', function($transaction) {
                    return $transaction->reseller->store_name;
                })
                ->addColumn('package', function($transaction) {
                    return $transaction->package->name;
                })
                ->addColumn('amount_formatted', function($transaction) {
                    return 'Rp ' . number_format($transaction->amount, 0, ',', '.');
                })
                ->addColumn('payment_status_badge', function($transaction) {
                    if ($transaction->payment_status === 'paid') {
                        return '<span class="badge bg-success">Paid</span>';
                    } elseif ($transaction->payment_status === 'pending') {
                        return '<span class="badge bg-warning">Pending</span>';
                    } elseif ($transaction->payment_status === 'expired') {
                        return '<span class="badge bg-danger">Expired</span>';
                    } else {
                        return '<span class="badge bg-danger">Failed</span>';
                    }
                })
                ->addColumn('date', function($transaction) {
                    return $transaction->created_at->format('d M Y H:i');
                })
                ->addColumn('actions', function($transaction) {
                    return '<a href="' . route('admin.transactions.membership.show', $transaction->id) . '" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i>
                    </a>';
                })
                ->rawColumns(['invoice', 'payment_status_badge', 'actions'])
                ->make(true);
        }
        
        return view('admin.transactions.membership-index');
    }
    
    /**
     * Display the specified membership transaction
     */
    public function membershipShow($id)
    {
        $transaction = MembershipTransaction::with([
            'reseller.user', 
            'package'
        ])->findOrFail($id);
        
        return view('admin.transactions.membership-show', compact('transaction'));
    }
}