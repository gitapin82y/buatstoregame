<?php
// app/Http/Controllers/User/DashboardController.php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display user dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Stats untuk dashboard
        $stats = [
            'total_transactions' => UserTransaction::where('user_id', $user->id)->count(),
            'completed_transactions' => UserTransaction::where('user_id', $user->id)
                ->where('process_status', 'completed')
                ->count(),
            'pending_transactions' => UserTransaction::where('user_id', $user->id)
                ->whereIn('process_status', ['pending', 'processing'])
                ->count(),
            'total_spent' => UserTransaction::where('user_id', $user->id)
                ->where('payment_status', 'paid')
                ->sum('amount'),
        ];
        
        // Transaksi terbaru
        $latestTransactions = UserTransaction::with(['game', 'service', 'reseller'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
            
        return view('user.dashboard', compact('user', 'stats', 'latestTransactions'));
    }
}





