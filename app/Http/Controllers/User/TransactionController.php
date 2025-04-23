<?php

// app/Http/Controllers/User/TransactionController.php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    /**
     * Display user transactions
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($request->ajax()) {
            $transactions = UserTransaction::with(['game', 'service', 'reseller'])
                ->where('user_id', $user->id);
                
            return DataTables::of($transactions)
                ->addColumn('invoice', function($transaction) {
                    return '<a href="' . route('user.transactions.show', $transaction->id) . '">' . 
                        $transaction->invoice_number . '</a>';
                })
                ->addColumn('product', function($transaction) {
                    return $transaction->game->name . ' - ' . $transaction->service->name;
                })
                ->addColumn('store', function($transaction) {
                    return $transaction->reseller->store_name;
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
                    return '<a href="' . route('user.transactions.show', $transaction->id) . '" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i>
                    </a>';
                    
                    // If transaction pending, add pay button
                    if ($transaction->payment_status === 'pending') {
                        return '<a href="' . $transaction->payment_link . '" class="btn btn-sm btn-primary" target="_blank">
                            <i class="fas fa-credit-card"></i> Pay
                        </a>';
                    }
                })
                ->rawColumns(['invoice', 'payment_status_badge', 'process_status_badge', 'actions'])
                ->make(true);
        }
        
        return view('user.transactions.index');
    }
    
    /**
     * Display transaction details
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $transaction = UserTransaction::with([
            'game', 
            'service', 
            'reseller',
            'option'
        ])
        ->where('user_id', $user->id)
        ->findOrFail($id);
        
        return view('user.transactions.show', compact('transaction'));
    }
    
    /**
     * Track transaction status
     */
    public function track(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|string',
        ]);
        
        $transaction = UserTransaction::with([
            'game', 
            'service', 
            'reseller',
            'option'
        ])
        ->where('invoice_number', $request->invoice_number)
        ->first();
        
        if (!$transaction) {
            return redirect()->route('user.transactions.track.form')
                ->with('error', 'Invoice tidak ditemukan. Periksa kembali nomor invoice Anda.');
        }
        
        return view('user.transactions.track', compact('transaction'));
    }
    
    /**
     * Show track transaction form
     */
    public function trackForm()
    {
        return view('user.transactions.track-form');
    }
}