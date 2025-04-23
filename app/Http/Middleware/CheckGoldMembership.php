<?php

// app/Http/Middleware/CheckGoldMembership.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckGoldMembership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if ($user->role !== 'reseller') {
            return redirect()->back();
        }
        
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        if ($reseller->membership_level !== 'gold' || !$reseller->isActive()) {
            return redirect()->route('reseller.membership.index')
                ->with('error', 'Fitur ini hanya tersedia untuk paket Gold yang aktif.');
        }

        return $next($request);
    }
}