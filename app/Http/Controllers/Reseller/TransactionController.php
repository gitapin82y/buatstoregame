<?php

// app/Http/Controllers/Reseller/TransactionController.php
namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        if ($request->ajax()) {
            $transactions = UserTransaction::with(['user', 'game', 'service'])
                ->where('reseller_id', $reseller->id);
                
            return DataTables::of($transactions)
                ->addColumn('invoice', function($transaction) {
                    return '<a href="' . route('reseller.transactions.show', $transaction->id) . '">' . 
                        $transaction->invoice_number . '</a>';
                })
                ->addColumn('customer', function($transaction) {
                    return $transaction->user->name;
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
                    return '<a href="' . route('reseller.transactions.show', $transaction->id) . '" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i>
                    </a>';
                })
                ->rawColumns(['invoice', 'payment_status_badge', 'process_status_badge', 'actions'])
                ->make(true);
        }
        
        // Stats
        $stats = [
            'total' => UserTransaction::where('reseller_id', $reseller->id)->count(),
            'paid' => UserTransaction::where('reseller_id', $reseller->id)
                ->where('payment_status', 'paid')
                ->count(),
            'completed' => UserTransaction::where('reseller_id', $reseller->id)
                ->where('process_status', 'completed')
                ->count(),
            'pending' => UserTransaction::where('reseller_id', $reseller->id)
                ->where('payment_status', 'pending')
                ->count(),
            'total_sales' => UserTransaction::where('reseller_id', $reseller->id)
                ->where('payment_status', 'paid')
                ->sum('amount'),
        ];
        
        return view('reseller.transactions.index', compact('stats'));
    }
    
    /**
     * Display the specified transaction
     */
    public function show($id)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        $transaction = UserTransaction::with([
            'user', 
            'game', 
            'service',
            'option'
        ])
        ->where('reseller_id', $reseller->id)
        ->findOrFail($id);
        
        return view('reseller.transactions.show', compact('transaction'));
    }
}





