<?php

// app/Http/Controllers/Admin/DashboardController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Game;
use App\Models\UserTransaction;
use App\Models\MembershipTransaction;
use App\Models\ResellerProfile;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Admin dashboard
     */
    public function index()
    {
        // Stats untuk dashboard
        $stats = [
            'total_users' => User::count(),
            'total_resellers' => User::where('role', 'reseller')->count(),
            'active_resellers' => ResellerProfile::where('membership_expires_at', '>', now())->count(),
            'total_games' => Game::count(),
            'total_transactions' => UserTransaction::count(),
            'transactions_today' => UserTransaction::whereDate('created_at', today())->count(),
            'sales_today' => UserTransaction::whereDate('created_at', today())->where('payment_status', 'paid')->sum('amount'),
            'pending_withdrawals' => Withdrawal::where('status', 'pending')->count(),
        ];
        
        // Chart data: transaksi 7 hari terakhir
        $transactionChart = UserTransaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" THEN amount ELSE 0 END) as amount')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Chart data: reseller baru per bulan
        $resellerChart = User::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as count')
            )
            ->where('role', 'reseller')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
            
        // Reseller yang akan segera expired
        $expiringResellers = ResellerProfile::with('user')
            ->where('membership_expires_at', '>', now())
            ->where('membership_expires_at', '<=', now()->addDays(7))
            ->orderBy('membership_expires_at')
            ->limit(5)
            ->get();
            
        // Transaksi terbaru
        $latestTransactions = UserTransaction::with(['user', 'reseller.user', 'game'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
            
        return view('admin.dashboard', compact(
            'stats', 
            'transactionChart', 
            'resellerChart', 
            'expiringResellers', 
            'latestTransactions'
        ));
    }
}