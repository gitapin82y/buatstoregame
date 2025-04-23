<?php

// app/Http/Controllers/Reseller/MembershipController.php
namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\MembershipPackage;
use App\Models\MembershipTransaction;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MembershipController extends Controller
{
    /**
     * Show membership packages
     */
    public function index()
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        // Get active packages
        $packages = MembershipPackage::where('status', 'active')->get();
        
        // Membership info
        $membershipInfo = [
            'level' => ucfirst($reseller->membership_level),
            'expires_at' => $reseller->membership_expires_at,
            'is_active' => $reseller->isActive(),
            'is_grace_period' => $reseller->isGracePeriod(),
            'domain' => $reseller->getDomainUrl(),
        ];

        // Get transaction history
        $transactions = MembershipTransaction::where('reseller_id', $reseller->id)
            ->with('package')
            ->orderByDesc('created_at')
            ->get();
            
        return view('reseller.membership.index', compact('packages', 'membershipInfo', 'transactions'));
    }
    
    /**
     * Show checkout page for a package
     */
    public function checkout($id)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        $package = MembershipPackage::where('status', 'active')
            ->findOrFail($id);
            
        return view('reseller.membership.checkout', compact('package', 'reseller'));
    }
    
    /**
     * Process membership purchase
     */
    public function purchase(Request $request, $id, PaymentService $paymentService)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        $package = MembershipPackage::where('status', 'active')
            ->findOrFail($id);
            
        // Generate invoice number
        $invoiceNumber = 'MEM-' . time() . '-' . $reseller->id;
        
        // Create transaction
        $transaction = MembershipTransaction::create([
            'reseller_id' => $reseller->id,
            'package_id' => $package->id,
            'invoice_number' => $invoiceNumber,
            'amount' => $package->price,
            'payment_status' => 'pending',
            'expired_at' => now()->addDay(),
        ]);
        
        // Create payment
        try {
            $paymentUrl = $paymentService->createMembershipInvoice($transaction);
            
            return redirect()->to($paymentUrl);
        } catch (\Exception $e) {
            return redirect()->route('reseller.membership.index')
                ->with('error', 'Terjadi kesalahan saat membuat pembayaran: ' . $e->getMessage());
        }
    }
    
    /**
     * Handle successful payment
     */
    public function paymentSuccess(Request $request, $id)
    {
        $transaction = MembershipTransaction::with(['reseller', 'package'])
            ->findOrFail($id);
            
        if ($transaction->payment_status !== 'paid') {
            return redirect()->route('reseller.membership.index')
                ->with('info', 'Pembayaran sedang diproses. Silakan cek status keanggotaan Anda dalam beberapa saat.');
        }
        
        return redirect()->route('reseller.membership.index')
            ->with('success', 'Pembayaran berhasil! Membership Anda telah diaktifkan.');
    }
    
    /**
     * Handle failed payment
     */
    public function paymentFailure(Request $request, $id)
    {
        $transaction = MembershipTransaction::findOrFail($id);
        
        return redirect()->route('reseller.membership.index')
            ->with('error', 'Pembayaran gagal atau dibatalkan. Silakan coba lagi.');
    }
}