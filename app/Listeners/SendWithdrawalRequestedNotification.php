<?php

// app/Listeners/SendWithdrawalRequestedNotification.php
namespace App\Listeners;

use App\Events\WithdrawalRequested;
use App\Notifications\WithdrawalRequested as WithdrawalRequestedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;

class SendWithdrawalRequestedNotification implements ShouldQueue
{
    /**
    * Handle the event.
    *
    * @param  WithdrawalRequested  $event
    * @return void
    */
    public function handle(WithdrawalRequested $event)
    {
        $withdrawal = $event->withdrawal;
        $user = $withdrawal->reseller->user;
        
        // Notify reseller
        $user->notify(new WithdrawalRequestedNotification($withdrawal));
        
        // Notify admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            // $admin->notify(new AdminWithdrawalRequestedNotification($withdrawal));
        }
    }
}