<?php

// app/Events/MembershipActivated.php
namespace App\Events;

use App\Models\MembershipTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MembershipActivated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction;

    /**
     * Create a new event instance.
     *
     * @param MembershipTransaction $transaction
     * @return void
     */
    public function __construct(MembershipTransaction $transaction)
    {
        $this->transaction = $transaction;
    }
}

// app/Events/TransactionPaid.php
namespace App\Events;

use App\Models\UserTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionPaid
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

// app/Events/TransactionCompleted.php
namespace App\Events;

use App\Models\UserTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionCompleted
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
    
    // Tambahkan file event lainnya sesuai kebutuhan: JokiOrderReceived, FormationOrderReceived, ServiceOrderReceived, dll.
      
    
    
 
    
 
    
    