<?php

namespace App\Providers;
    
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        // Custom events
        \App\Events\MembershipActivated::class => [
            \App\Listeners\SendMembershipActivationNotification::class,
        ],
        \App\Events\TransactionPaid::class => [
            \App\Listeners\SendTransactionPaidNotification::class,
        ],
        \App\Events\TransactionCompleted::class => [
            \App\Listeners\SendTransactionCompletedNotification::class,
        ],
        \App\Events\WithdrawalRequested::class => [
            \App\Listeners\SendWithdrawalRequestedNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}