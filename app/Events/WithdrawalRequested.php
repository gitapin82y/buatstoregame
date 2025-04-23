<?php
// app/Events/WithdrawalRequested.php
namespace App\Events;

use App\Models\Withdrawal;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WithdrawalRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $withdrawal;

    /**
     * Create a new event instance.
     *
     * @param Withdrawal $withdrawal
     * @return void
     */
    public function __construct(Withdrawal $withdrawal)
    {
        $this->withdrawal = $withdrawal;
    }
}