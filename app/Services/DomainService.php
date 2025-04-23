<?php

namespace App\Services;

use App\Models\DomainCheck;
use App\Models\ResellerProfile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class DomainService
{
    /**
     * Check if a domain is available
     */
    public function checkDomainAvailability($domain)
    {
        // Check if we already have a recent check
        $existingCheck = DomainCheck::where('domain', $domain)
            ->where('checked_at', '>', now()->subDay())
            ->first();
            
        if ($existingCheck) {
            return $existingCheck->status === 'available';
        }
        
        try {
            // Gunakan Whois API untuk memeriksa ketersediaan domain
            // Contoh menggunakan API domain-availability.whoisxmlapi.com
            $apiKey = config('services.whoisxml.api_key', 'at_demo_key');
            $response = Http::get("https://domain-availability.whoisxmlapi.com/api/v1", [
                'apiKey' => $apiKey,
                'domainName' => $domain,
                'credits' => 1
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                $isAvailable = $result['DomainInfo']['domainAvailability'] === 'AVAILABLE';
                
                // Store result in database
                DomainCheck::create([
                    'domain' => $domain,
                    'status' => $isAvailable ? 'available' : 'taken',
                    'checked_at' => now(),
                ]);
                
                return $isAvailable;
            }
            
            // Fallback jika API error: anggap domain tidak tersedia
            DomainCheck::create([
                'domain' => $domain,
                'status' => 'taken',
                'checked_at' => now(),
            ]);
            
            return false;
        } catch (Exception $e) {
            Log::error('Error checking domain availability: ' . $e->getMessage());
            
            // Fallback jika error: anggap domain tidak tersedia
            DomainCheck::create([
                'domain' => $domain,
                'status' => 'taken',
                'checked_at' => now(),
            ]);
            
            return false;
        }
    }
    
    /**
     * Check if subdomain is available
     */
    public function checkSubdomainAvailability($subdomain)
    {
        // Periksa apakah subdomain sudah digunakan
        $exists = ResellerProfile::where('subdomain', $subdomain)->exists();
        return !$exists;
    }
    
    /**
     * Validate and format domain
     */
    public function formatDomain($domain)
    {
        // Remove http/https
        $domain = preg_replace('#^https?://#', '', $domain);
        
        // Remove www if exists
        $domain = preg_replace('/^www\./', '', $domain);
        
        // Remove trailing slashes and paths
        $domain = strtok($domain, '/');
        
        return strtolower(trim($domain));
    }
    
    /**
     * Validate and format subdomain
     */
    public function formatSubdomain($subdomain)
    {
        // Only allow alphanumeric and hyphens
        $subdomain = preg_replace('/[^a-z0-9\-]/', '', strtolower($subdomain));
        
        // Remove consecutive hyphens
        $subdomain = preg_replace('/-+/', '-', $subdomain);
        
        // Trim hyphens from beginning and end
        return trim($subdomain, '-');
    }
    
    /**
     * Set up custom domain for reseller
     */
    public function setupCustomDomain(ResellerProfile $reseller, $domain)
    {
        // Format domain
        $domain = $this->formatDomain($domain);
        
        // Check availability (just to update our database)
        $this->checkDomainAvailability($domain);
        
        // Set custom domain for reseller
        $reseller->update([
            'custom_domain' => $domain
        ]);
        
        // Return DNS instructions
        return [
            'domain' => $domain,
            'instructions' => [
                'type' => 'A',
                'name' => '@',
                'value' => config('app.server_ip', '123.456.789.10'),
                'ttl' => '3600'
            ]
        ];
    }
}