<?php

// app/Http/Controllers/Reseller/DashboardController.php
namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\UserTransaction;
use App\Models\Game;
use App\Models\ResellerGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show reseller dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        // Stats untuk dashboard
        $stats = [
            'balance' => $reseller->balance,
            'transactions_count' => UserTransaction::where('reseller_id', $reseller->id)->count(),
            'paid_transactions' => UserTransaction::where('reseller_id', $reseller->id)
                ->where('payment_status', 'paid')
                ->count(),
            'total_sales' => UserTransaction::where('reseller_id', $reseller->id)
                ->where('payment_status', 'paid')
                ->sum('amount'),
            'active_games' => ResellerGame::where('reseller_id', $reseller->id)
                ->where('is_active', true)
                ->count(),
        ];
        
        // Chart data: transaksi 7 hari terakhir
        $transactionChart = UserTransaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" THEN amount ELSE 0 END) as amount')
            )
            ->where('reseller_id', $reseller->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Game stats
        $gameStats = UserTransaction::select(
                'game_id',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" THEN amount ELSE 0 END) as amount')
            )
            ->where('reseller_id', $reseller->id)
            ->where('payment_status', 'paid')
            ->groupBy('game_id')
            ->with('game')
            ->orderByDesc('amount')
            ->limit(5)
            ->get();
            
        // Transaksi terbaru
        $latestTransactions = UserTransaction::with(['user', 'game', 'service'])
            ->where('reseller_id', $reseller->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
            
        // Membership info
        $membershipInfo = [
            'level' => ucfirst($reseller->membership_level),
            'expires_at' => $reseller->membership_expires_at,
            'is_active' => $reseller->isActive(),
            'is_grace_period' => $reseller->isGracePeriod(),
            'domain' => $reseller->getDomainUrl(),
        ];
        
        return view('reseller.dashboard', compact(
            'reseller',
            'stats',
            'transactionChart',
            'gameStats',
            'latestTransactions',
            'membershipInfo'
        ));
    }
    
    /**
     * Setup reseller profile
     */
    public function setup()
    {
        $user = Auth::user();
        
        // Jika user sudah punya profil reseller, redirect ke dashboard
        if ($user->resellerProfile) {
            return redirect()->route('reseller.dashboard');
        }
        
        return view('reseller.setup');
    }
    
    /**
     * Save reseller profile setup
     */
    public function storeSetup(Request $request)
    {
        $user = Auth::user();
        
        // Jika user sudah punya profil reseller, redirect ke dashboard
        if ($user->resellerProfile) {
            return redirect()->route('reseller.dashboard');
        }
        
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_description' => 'nullable|string',
            'subdomain' => 'required|string|min:3|max:30|regex:/^[a-z0-9\-]+$/|unique:reseller_profiles',
        ]);
        
        // Buat subdomain
        $subdomain = app(\App\Services\DomainService::class)->formatSubdomain($request->subdomain);
        
        // Buat profil reseller
        $resellerProfile = \App\Models\ResellerProfile::create([
            'user_id' => $user->id,
            'store_name' => $request->store_name,
            'store_description' => $request->store_description,
            'store_theme_color' => '#3490dc', // Default color
            'membership_level' => 'silver', // Default level
            'membership_expires_at' => now()->addDays(7), // Trial period 7 hari
            'subdomain' => $subdomain,
        ]);
        
        return redirect()->route('reseller.dashboard')
            ->with('success', 'Profil reseller berhasil dibuat! Anda mendapatkan trial 7 hari untuk paket Silver.');
    }
}


    
    
    