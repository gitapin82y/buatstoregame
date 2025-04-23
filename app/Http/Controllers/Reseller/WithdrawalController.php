<?php

// app/Http/Controllers/Reseller/WithdrawalController.php
namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class WithdrawalController extends Controller
{
    /**
     * Display withdrawal history and form
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        if ($request->ajax()) {
            $withdrawals = Withdrawal::where('reseller_id', $reseller->id);
                
            return DataTables::of($withdrawals)
                ->addColumn('amount_formatted', function($withdrawal) {
                    return 'Rp ' . number_format($withdrawal->amount, 0, ',', '.');
                })
                ->addColumn('bank_account', function($withdrawal) {
                    return $withdrawal->bank_name . ' - ' . $withdrawal->account_number . ' - ' . $withdrawal->account_name;
                })
                ->addColumn('status_badge', function($withdrawal) {
                    if ($withdrawal->status === 'approved') {
                        return '<span class="badge bg-success">Approved</span>';
                    } elseif ($withdrawal->status === 'pending') {
                        return '<span class="badge bg-warning">Pending</span>';
                    } else {
                        return '<span class="badge bg-danger">Rejected</span>';
                    }
                })
                ->addColumn('date', function($withdrawal) {
                    return $withdrawal->created_at->format('d M Y H:i');
                })
                ->addColumn('actions', function($withdrawal) {
                    if ($withdrawal->status === 'approved') {
                        return '<button type="button" data-id="' . $withdrawal->id . '" class="btn btn-sm btn-info btn-proof">
                            <i class="fas fa-image"></i> View Proof
                        </button>';
                    } elseif ($withdrawal->status === 'rejected') {
                        return '<button type="button" data-id="' . $withdrawal->id . '" class="btn btn-sm btn-danger btn-reason">
                            <i class="fas fa-times"></i> Rejection Reason
                        </button>';
                    } else {
                        return '-';
                    }
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }
        
        // Current balance
        $balance = $reseller->balance;
        
        // Stats
        $stats = [
            'total_withdrawals' => Withdrawal::where('reseller_id', $reseller->id)->count(),
            'approved_withdrawals' => Withdrawal::where('reseller_id', $reseller->id)
                ->where('status', 'approved')
                ->count(),
            'pending_withdrawals' => Withdrawal::where('reseller_id', $reseller->id)
                ->where('status', 'pending')
                ->count(),
            'total_withdrawn' => Withdrawal::where('reseller_id', $reseller->id)
                ->where('status', 'approved')
                ->sum('amount'),
        ];
        
        return view('reseller.withdrawals.index', compact('balance', 'stats'));
    }
    
    /**
     * Request withdrawal
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        $request->validate([
            'amount' => 'required|numeric|min:50000|max:' . $reseller->balance,
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
        ]);
        
        // Create withdrawal request
        $withdrawal = Withdrawal::create([
            'reseller_id' => $reseller->id,
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'status' => 'pending',
        ]);
        
        // Deduct balance
        $reseller->decrement('balance', $request->amount);
        
        // Notify admin
        // event(new \App\Events\WithdrawalRequested($withdrawal));
        
        return redirect()->route('reseller.withdrawals.index')
            ->with('success', 'Permintaan penarikan berhasil dibuat dan sedang diproses.');
    }
    
    /**
     * Get withdrawal proof
     */
    public function getProof($id)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return response()->json([
                'success' => false,
                'message' => 'Reseller profile not found'
            ]);
        }
        
        $withdrawal = Withdrawal::where('reseller_id', $reseller->id)
            ->where('id', $id)
            ->where('status', 'approved')
            ->firstOrFail();
            
        return response()->json([
            'success' => true,
            'proof_url' => $withdrawal->proof_image ? asset('storage/' . $withdrawal->proof_image) : null,
            'amount' => 'Rp ' . number_format($withdrawal->amount, 0, ',', '.'),
            'date' => $withdrawal->approved_at->format('d M Y H:i')
        ]);
    }
    
    /**
     * Get rejection reason
     */
    public function getRejectionReason($id)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return response()->json([
                'success' => false,
                'message' => 'Reseller profile not found'
            ]);
        }
        
        $withdrawal = Withdrawal::where('reseller_id', $reseller->id)
            ->where('id', $id)
            ->where('status', 'rejected')
            ->firstOrFail();
            
        return response()->json([
            'success' => true,
            'reason' => $withdrawal->rejection_reason,
            'amount' => 'Rp ' . number_format($withdrawal->amount, 0, ',', '.'),
            'date' => $withdrawal->rejected_at->format('d M Y H:i')
        ]);
    }
}