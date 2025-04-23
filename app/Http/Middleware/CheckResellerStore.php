<?php

// app/Http/Middleware/CheckResellerStore.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ResellerProfile;

class CheckResellerStore
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
        $domain = $request->route('domain');
        
        // Find reseller by subdomain or custom domain
        $reseller = ResellerProfile::where('subdomain', $domain)
            ->orWhere('custom_domain', $domain)
            ->first();
            
        if (!$reseller) {
            abort(404, 'Store not found');
        }
        
        // Check if reseller account is active
        if (!$reseller->isActive() && !$reseller->isGracePeriod()) {
            return response()->view('store.inactive', compact('reseller'));
        }
        
        // Add reseller to the request
        $request->attributes->add(['reseller' => $reseller]);
        
        return $next($request);
    }
}