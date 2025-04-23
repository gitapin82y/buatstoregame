<?php


// app/Http/Middleware/CheckActiveMembership.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActiveMembership
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
        
        if (!$reseller->isActive()) {
            return redirect()->route('reseller.membership.index')
                ->with('error', 'Keanggotaan Anda telah kedaluwarsa. Silakan perpanjang untuk mengakses fitur ini.');
        }

        return $next($request);
    }
}
