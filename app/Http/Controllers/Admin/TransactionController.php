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

    /**
 * Export transactions to CSV
 */
public function export(Request $request)
{
    // Your export logic here
    // For now, we'll create a simplified version
    return response()->streamDownload(function () use ($request) {
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, [
            'Invoice', 'Customer', 'Reseller', 'Game', 'Service', 'Amount', 
            'Payment Status', 'Process Status', 'Date'
        ]);
        
        // Query with filters
        $transactions = UserTransaction::with(['user', 'reseller.user', 'game', 'service'])
            ->when($request->filled('payment_status'), function ($q) use ($request) {
                return $q->where('payment_status', $request->payment_status);
            })
            ->when($request->filled('process_status'), function ($q) use ($request) {
                return $q->where('process_status', $request->process_status);
            })
            ->when($request->filled('reseller_id'), function ($q) use ($request) {
                return $q->where('reseller_id', $request->reseller_id);
            })
            ->when($request->filled('game_id'), function ($q) use ($request) {
                return $q->where('game_id', $request->game_id);
            })
            ->when($request->filled('date_from'), function ($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->date_to);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                return $q->where('invoice_number', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Data
        foreach ($transactions as $transaction) {
            fputcsv($output, [
                $transaction->invoice_number,
                $transaction->user->name,
                $transaction->reseller->store_name,
                $transaction->game->name,
                $transaction->service->name,
                $transaction->amount,
                $transaction->payment_status,
                $transaction->process_status,
                $transaction->created_at->format('Y-m-d H:i:s')
            ]);
        }
        
        fclose($output);
    }, 'transactions-' . date('Y-m-d') . '.csv', [
        'Content-Type' => 'text/csv',
    ]);
}

/**
 * Export membership transactions to CSV
 */
public function membershipExport(Request $request)
{
    // Similar to above but for membership transactions
    return response()->streamDownload(function () use ($request) {
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, [
            'Invoice', 'Reseller', 'Package', 'Level', 'Amount', 
            'Payment Status', 'Date'
        ]);
        
        // Query with filters
        $transactions = MembershipTransaction::with(['reseller.user', 'package'])
            ->when($request->filled('payment_status'), function ($q) use ($request) {
                return $q->where('payment_status', $request->payment_status);
            })
            ->when($request->filled('package_id'), function ($q) use ($request) {
                return $q->where('package_id', $request->package_id);
            })
            ->when($request->filled('reseller_id'), function ($q) use ($request) {
                return $q->where('reseller_id', $request->reseller_id);
            })
            ->when($request->filled('date_from'), function ($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->date_to);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                return $q->where('invoice_number', 'like', '%' . $request->search . '%')
                    ->orWhereHas('reseller.user', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Data
        foreach ($transactions as $transaction) {
            fputcsv($output, [
                $transaction->invoice_number,
                $transaction->reseller->store_name,
                $transaction->package->name,
                $transaction->package->level,
                $transaction->amount,
                $transaction->payment_status,
                $transaction->created_at->format('Y-m-d H:i:s')
            ]);
        }
        
        fclose($output);
    }, 'membership-transactions-' . date('Y-m-d') . '.csv', [
        'Content-Type' => 'text/csv',
    ]);
}

/**
 * Check payment status for a transaction
 */
public function checkPayment($id)
{
    $transaction = UserTransaction::findOrFail($id);
    
    if ($transaction->payment_status !== 'pending') {
        return response()->json([
            'success' => false,
            'message' => 'This transaction is not in pending status.'
        ]);
    }
    
    // Here you would typically check with Xendit API
    // For now, let's simulate a check
    
    if ($transaction->expired_at && $transaction->expired_at->isPast()) {
        $transaction->update([
            'payment_status' => 'expired'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Transaction has expired.'
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'Payment is still pending.'
    ]);
}

/**
 * Check payment status for a membership transaction
 */
public function checkMembershipPayment($id)
{
    $transaction = MembershipTransaction::findOrFail($id);
    
    if ($transaction->payment_status !== 'pending') {
        return response()->json([
            'success' => false,
            'message' => 'This transaction is not in pending status.'
        ]);
    }
    
    // Here you would typically check with Xendit API
    // For now, let's simulate a check
    
    if ($transaction->expired_at && $transaction->expired_at->isPast()) {
        $transaction->update([
            'payment_status' => 'expired'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Transaction has expired.'
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'Payment is still pending.'
    ]);
}
}