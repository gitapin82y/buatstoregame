<?php
namespace App\Listeners;

use App\Events\TransactionCompleted;
use App\Notifications\TransactionCompleted as TransactionCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTransactionCompletedNotification implements ShouldQueue
{
    /**
    * Handle the event.
    *
    * @param  TransactionCompleted  $event
    * @return void
    */
    public function handle(TransactionCompleted $event)
    {
        $transaction = $event->transaction;
        $user = $transaction->user;
        
        $user->notify(new TransactionCompletedNotification($transaction));
    }
}