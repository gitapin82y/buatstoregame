<?php

namespace App\Listeners;
    
use App\Events\MembershipActivated;
use App\Notifications\MembershipActivated as MembershipActivatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMembershipActivationNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  MembershipActivated  $event
     * @return void
     */
    public function handle(MembershipActivated $event)
    {
        $transaction = $event->transaction;
        $user = $transaction->reseller->user;
        
        $user->notify(new MembershipActivatedNotification($transaction));
    }
}