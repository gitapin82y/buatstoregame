<?php

// app/Http/Controllers/Admin/WithdrawalController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the withdrawals
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $withdrawals = Withdrawal::with(['reseller.user']);
                
            return DataTables::of($withdrawals)
                ->addColumn('reseller', function($withdrawal) {
                    return $withdrawal->reseller->store_name;
                })
                ->addColumn('bank_account', function($withdrawal) {
                    return $withdrawal->bank_name . ' - ' . $withdrawal->account_number . ' - ' . $withdrawal->account_name;
                })
                ->addColumn('amount_formatted', function($withdrawal) {
                    return 'Rp ' . number_format($withdrawal->amount, 0, ',', '.');
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
                    $actions = '<a href="' . route('admin.withdrawals.show', $withdrawal->id) . '" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i>
                    </a>';
                    
                    if ($withdrawal->status === 'pending') {
                        $actions .= ' <button type="button" data-id="' . $withdrawal->id . '" class="btn btn-sm btn-success btn-approve">
                            <i class="fas fa-check"></i> Approve
                        </button>';
                        $actions .= ' <button type="button" data-id="' . $withdrawal->id . '" class="btn btn-sm btn-danger btn-reject">
                            <i class="fas fa-times"></i> Reject
                        </button>';
                    }
                    
                    return $actions;
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }
        
        // Stats
        $stats = [
            'total' => Withdrawal::count(),
            'pending' => Withdrawal::where('status', 'pending')->count(),
            'approved' => Withdrawal::where('status', 'approved')->count(),
            'rejected' => Withdrawal::where('status', 'rejected')->count(),
            'pending_amount' => Withdrawal::where('status', 'pending')->sum('amount'),
        ];
        
        return view('admin.withdrawals.index', compact('stats'));
    }
    
    /**
     * Display the specified withdrawal
     */
    public function show($id)
    {
        $withdrawal = Withdrawal::with(['reseller.user'])->findOrFail($id);
        return view('admin.withdrawals.show', compact('withdrawal'));
    }
    
    /**
     * Approve the specified withdrawal
     */
    public function approve(Request $request, $id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Withdrawal sudah diproses sebelumnya!'
            ]);
        }
        
        $request->validate([
            'proof_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Upload bukti pembayaran
        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('withdrawals/proofs', 'public');
        }
        
        // Update withdrawal
        $withdrawal->update([
            'status' => 'approved',
            'proof_image' => $proofPath,
            'approved_at' => now(),
        ]);
        
        // Send notification
        $withdrawal->reseller->user->notify(new \App\Notifications\WithdrawalApproved($withdrawal));
        
        return response()->json([
            'success' => true,
            'message' => 'Withdrawal berhasil disetujui!'
        ]);
    }
    
    /**
     * Reject the specified withdrawal
     */
    public function reject(Request $request, $id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Withdrawal sudah diproses sebelumnya!'
            ]);
        }
        
        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);
        
        // Update withdrawal
        $withdrawal->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
        ]);
        
        // Refund balance ke reseller
        $withdrawal->reseller->increment('balance', $withdrawal->amount);
        
        // Send notification
        $withdrawal->reseller->user->notify(new \App\Notifications\WithdrawalRejected($withdrawal));
        
        return response()->json([
            'success' => true,
            'message' => 'Withdrawal berhasil ditolak dan dana dikembalikan ke saldo reseller!'
        ]);
    }
}