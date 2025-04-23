<?php

// app/Http/Controllers/WebhookController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\PaymentService;

class WebhookController extends Controller
{
    /**
     * Handle Xendit webhook for user transactions
     */
    public function xenditTransaction(Request $request, PaymentService $paymentService)
    {
        // Log the webhook request for debugging
        Log::info('Xendit Transaction Webhook received', $request->all());
        
        // Verify Xendit callback token if needed
        // $this->verifyXenditCallback($request);
        
        // Handle the callback
        try {
            $success = $paymentService->handleUserTransactionCallback($request->all());
            
            if ($success) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Could not process the callback']);
            }
        } catch (\Exception $e) {
            Log::error('Error processing Xendit transaction webhook: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Handle Xendit webhook for membership transactions
     */
    public function xenditMembership(Request $request, PaymentService $paymentService)
    {
        // Log the webhook request for debugging
        Log::info('Xendit Membership Webhook received', $request->all());
        
        // Verify Xendit callback token if needed
        // $this->verifyXenditCallback($request);
        
        // Handle the callback
        try {
            $success = $paymentService->handleMembershipCallback($request->all());
            
            if ($success) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Could not process the callback']);
            }
        } catch (\Exception $e) {
            Log::error('Error processing Xendit membership webhook: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Verify Xendit callback using token
     */
    private function verifyXenditCallback(Request $request)
    {
        $callbackToken = $request->header('X-CALLBACK-TOKEN');
        $xenditCallbackToken = config('xendit.callback_token');
        
        if (!$callbackToken || $callbackToken !== $xenditCallbackToken) {
            Log::warning('Invalid Xendit callback token');
            abort(403, 'Invalid callback token');
        }
    }
}