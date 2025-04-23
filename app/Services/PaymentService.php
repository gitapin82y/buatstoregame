<?php

namespace App\Services;

use App\Models\MembershipTransaction;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentService
{
    /**
     * Buat invoice Xendit untuk transaksi membership
     */
    public function createMembershipInvoice(MembershipTransaction $transaction)
    {
        try {
            // Set Xendit API key
            \Xendit\Xendit::setApiKey(config('xendit.secret_key'));
            
            // Generate unique external ID
            $externalId = 'membership-' . $transaction->invoice_number;
            
            // Get user details
            $reseller = $transaction->reseller;
            $user = $reseller->user;
            
            // Generate description
            $description = "Membership {$transaction->package->name} untuk {$reseller->store_name}";
            
            // Set callback URLs
            $successRedirectUrl = route('reseller.membership.payment.success', $transaction->id);
            $failureRedirectUrl = route('reseller.membership.payment.failure', $transaction->id);
            $callbackUrl = route('webhook.xendit.membership');
            
            // Generate invoice parameters
            $params = [
                'external_id' => $externalId,
                'amount' => $transaction->amount,
                'description' => $description,
                'invoice_duration' => 86400, // 24 jam
                'customer' => [
                    'given_names' => $user->name,
                    'email' => $user->email,
                    'mobile_number' => $user->phone_number ?? '',
                ],
                'success_redirect_url' => $successRedirectUrl,
                'failure_redirect_url' => $failureRedirectUrl,
                'callback_url' => $callbackUrl,
                'currency' => 'IDR',
            ];
            
            // Create invoice
            $invoice = \Xendit\Invoice::create($params);
            
            // Update transaction data
            $transaction->update([
                'xendit_invoice_id' => $invoice['id'],
                'payment_link' => $invoice['invoice_url'],
                'expired_at' => now()->addDay(),
            ]);
            
            return $invoice['invoice_url'];
        } catch (Exception $e) {
            Log::error('Error creating Xendit invoice: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Buat invoice Xendit untuk transaksi user
     */
    public function createUserInvoice(UserTransaction $transaction)
    {
        try {
            // Set Xendit API key
            \Xendit\Xendit::setApiKey(config('xendit.secret_key'));
            
            // Generate unique external ID
            $externalId = 'transaction-' . $transaction->invoice_number;
            
            // Get service details
            $game = $transaction->game->name;
            $service = $transaction->service->name;
            $user = $transaction->user;
            $reseller = $transaction->reseller;
            
            // Generate description
            $description = "{$service} {$game} via {$reseller->store_name}";
            
            // Set callback URLs
            $successRedirectUrl = route('store.payment.success', [
                'invoice' => $transaction->invoice_number,
                'domain' => $reseller->subdomain ?? $reseller->custom_domain
            ]);
            $failureRedirectUrl = route('store.payment.failure', [
                'invoice' => $transaction->invoice_number,
                'domain' => $reseller->subdomain ?? $reseller->custom_domain
            ]);
            $callbackUrl = route('webhook.xendit.transaction');
            
            // Generate invoice parameters
            $params = [
                'external_id' => $externalId,
                'amount' => $transaction->amount,
                'description' => $description,
                'invoice_duration' => 86400, // 24 jam
                'customer' => [
                    'given_names' => $user->name,
                    'email' => $user->email,
                    'mobile_number' => $user->phone_number ?? '',
                ],
                'success_redirect_url' => $successRedirectUrl,
                'failure_redirect_url' => $failureRedirectUrl,
                'callback_url' => $callbackUrl,
                'currency' => 'IDR',
            ];
            
            // Create invoice
            $invoice = \Xendit\Invoice::create($params);
            
            // Update transaction data
            $transaction->update([
                'xendit_invoice_id' => $invoice['id'],
                'payment_link' => $invoice['invoice_url'],
                'expired_at' => now()->addDay(),
            ]);
            
            return $invoice['invoice_url'];
        } catch (Exception $e) {
            Log::error('Error creating Xendit invoice: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Handle webhook callback dari Xendit untuk transaksi membership
     */
    public function handleMembershipCallback($payload)
    {
        try {
            // Verifikasi callback signature jika diperlukan
            
            $externalId = $payload['external_id'];
            $status = $payload['status'];
            
            // Extract invoice number from external_id (membership-XXXXX)
            $invoiceNumber = str_replace('membership-', '', $externalId);
            
            // Find transaction
            $transaction = MembershipTransaction::where('invoice_number', $invoiceNumber)->first();
            
            if (!$transaction) {
                Log::error('Membership transaction not found: ' . $invoiceNumber);
                return false;
            }
            
            if ($status === 'PAID' || $status === 'SETTLED') {
                // Update transaction status
                $transaction->update([
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                ]);
                
                // Aktivasi atau perpanjang membership reseller
                $package = $transaction->package;
                $reseller = $transaction->reseller;
                
                // Jika reseller masih aktif, tambahkan durasi
                if ($reseller->isActive()) {
                    $newExpiryDate = $reseller->membership_expires_at->addDays($package->duration_days);
                } else {
                    $newExpiryDate = now()->addDays($package->duration_days);
                }
                
                $reseller->update([
                    'membership_level' => $package->level,
                    'membership_expires_at' => $newExpiryDate,
                ]);
                
                // Kirim email notifikasi
                event(new \App\Events\MembershipActivated($transaction));
                
                return true;
            } elseif ($status === 'EXPIRED') {
                $transaction->update([
                    'payment_status' => 'expired',
                ]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            Log::error('Error handling Xendit membership callback: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Handle webhook callback dari Xendit untuk transaksi user
     */
    public function handleUserTransactionCallback($payload)
    {
        try {
            // Verifikasi callback signature jika diperlukan
            
            $externalId = $payload['external_id'];
            $status = $payload['status'];
            
            // Extract invoice number from external_id (transaction-XXXXX)
            $invoiceNumber = str_replace('transaction-', '', $externalId);
            
            // Find transaction
            $transaction = UserTransaction::where('invoice_number', $invoiceNumber)->first();
            
            if (!$transaction) {
                Log::error('User transaction not found: ' . $invoiceNumber);
                return false;
            }
            
            if ($status === 'PAID' || $status === 'SETTLED') {
                // Update transaction status
                $transaction->update([
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                    'process_status' => 'processing',
                ]);
                
                // Add balance to reseller
                $reseller = $transaction->reseller;
                $resellerProfit = $this->calculateResellerProfit($transaction);
                $reseller->increment('balance', $resellerProfit);
                
                // Process affiliate if exists
                $this->processAffiliateCommission($transaction);
                
                // Execute game top-up or service process via GameApiService
                app(GameApiService::class)->processTransaction($transaction);
                
                // Send notification
                event(new \App\Events\TransactionPaid($transaction));
                
                return true;
            } elseif ($status === 'EXPIRED') {
                $transaction->update([
                    'payment_status' => 'expired',
                ]);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            Log::error('Error handling Xendit user transaction callback: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Calculate reseller profit from a transaction
     */
    public function calculateResellerProfit(UserTransaction $transaction)
    {
        $serviceOption = $transaction->option;
        $basePrice = $serviceOption->base_price;
        $sellingPrice = $transaction->amount;
        
        $profit = $sellingPrice - $basePrice;
        
        // Jika ada affiliate terkait, kurangi komisi
        $affiliateTransaction = $transaction->affiliateTransaction;
        if ($affiliateTransaction) {
            $profit -= $affiliateTransaction->commission_amount;
        }
        
        return max(0, $profit);
    }
    
    /**
     * Process affiliate commission if applicable
     */
    private function processAffiliateCommission(UserTransaction $transaction)
    {
        // Check if affiliate code was used
        $affiliateCode = session('affiliate_code');
        if (!$affiliateCode) {
            return;
        }
        
        // Find affiliate
        $affiliate = \App\Models\Affiliate::where('referral_code', $affiliateCode)
            ->where('status', 'active')
            ->first();
            
        if (!$affiliate) {
            return;
        }
        
        // Calculate commission
        $commissionRate = $affiliate->commission_rate;
        $amount = $transaction->amount;
        $commissionAmount = $amount * ($commissionRate / 100);
        
        // Create affiliate transaction
        $affiliateTransaction = \App\Models\AffiliateTransaction::create([
            'affiliate_id' => $affiliate->id,
            'transaction_id' => $transaction->id,
            'commission_amount' => $commissionAmount,
            'status' => 'pending'
        ]);
        
        // Update affiliate earnings
        $affiliate->increment('earnings', $commissionAmount);
        
        // Clear session
        session()->forget('affiliate_code');
        
        return $affiliateTransaction;
    }
    
    /**
     * Verify payment for transaction (manual check)
     */
    public function verifyPayment($transactionId)
    {
        try {
            // Set Xendit API key
            \Xendit\Xendit::setApiKey(config('xendit.secret_key'));
            
            $transaction = UserTransaction::findOrFail($transactionId);
            
            if (!$transaction->xendit_invoice_id) {
                return false;
            }
            
            // Get invoice from Xendit
            $invoice = \Xendit\Invoice::retrieve($transaction->xendit_invoice_id);
            
            if ($invoice['status'] === 'PAID' || $invoice['status'] === 'SETTLED') {
                // Update transaction if not already paid
                if ($transaction->payment_status !== 'paid') {
                    $transaction->update([
                        'payment_status' => 'paid',
                        'paid_at' => now(),
                        'process_status' => 'processing',
                    ]);
                    
                    // Add balance to reseller
                    $reseller = $transaction->reseller;
                    $resellerProfit = $this->calculateResellerProfit($transaction);
                    $reseller->increment('balance', $resellerProfit);
                    
                    // Execute game top-up or service process via GameApiService
                    app(GameApiService::class)->processTransaction($transaction);
                    
                    // Send notification
                    event(new \App\Events\TransactionPaid($transaction));
                }
                
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            Log::error('Error verifying payment: ' . $e->getMessage());
            return false;
        }
    }
}