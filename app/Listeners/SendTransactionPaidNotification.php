<?php

// app/Listeners/SendTransactionPaidNotification.php
namespace App\Listeners;
    
use App\Events\TransactionPaid;
use App\Notifications\TransactionPaid as TransactionPaidNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTransactionPaidNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  TransactionPaid  $event
     * @return void
     */
    public function handle(TransactionPaid $event)
    {
        $transaction = $event->transaction;
        $user = $transaction->user;
        $reseller = $transaction->reseller->user;
        
        // Notify customer
        $user->notify(new TransactionPaidNotification($transaction));
        
        // Create and send notification to reseller (custom notification)
        // $reseller->notify(new ResellerOrderNotification($transaction));
    }
}
