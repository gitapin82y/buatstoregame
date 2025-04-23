<?php

namespace App\Events;

use App\Models\UserTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction;
    /**
     * Create a new event instance.
     *
     * @param UserTransaction $transaction
     * @return void
     */
    public function __construct(UserTransaction $transaction)
    {
        $this->transaction = $transaction;
    }
}