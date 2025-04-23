<?php

// app/Http/Controllers/Reseller/ProfileController.php
namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\ResellerProfile;
use App\Services\DomainService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show profile page
     */
    public function index()
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        return view('reseller.profile.index', compact('user', 'reseller'));
    }
    
        public function update(Request $request)
        {
            $user = Auth::user();
            $reseller = $user->resellerProfile;
            
            if (!$reseller) {
                return redirect()->route('reseller.setup');
            }
            
            $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => 'nullable|string|max:20',
                'store_name' => 'required|string|max:255',
                'store_description' => 'nullable|string',
                'store_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'store_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'store_theme_color' => 'required|string|max:7',
                'social_facebook' => 'nullable|string|max:255',
                'social_instagram' => 'nullable|string|max:255',
                'social_twitter' => 'nullable|string|max:255',
                'social_tiktok' => 'nullable|string|max:255',
            ]);
            
            // Update user data
            $user->update([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
            ]);
            
            // Upload logo jika ada
            if ($request->hasFile('store_logo')) {
                // Hapus logo lama jika ada
                if ($reseller->store_logo) {
                    Storage::disk('public')->delete($reseller->store_logo);
                }
                
                $logoPath = $request->file('store_logo')->store('resellers/logos', 'public');
                $reseller->store_logo = $logoPath;
            }
            
            // Upload banner jika ada
            if ($request->hasFile('store_banner')) {
                // Hapus banner lama jika ada
                if ($reseller->store_banner) {
                    Storage::disk('public')->delete($reseller->store_banner);
                }
                
                $bannerPath = $request->file('store_banner')->store('resellers/banners', 'public');
                $reseller->store_banner = $bannerPath;
            }
            
            // Update reseller profile
            $reseller->update([
                'store_name' => $request->store_name,
                'store_description' => $request->store_description,
                'store_theme_color' => $request->store_theme_color,
                'social_facebook' => $request->social_facebook,
                'social_instagram' => $request->social_instagram,
                'social_twitter' => $request->social_twitter,
                'social_tiktok' => $request->social_tiktok,
            ]);
            
            return redirect()->route('reseller.profile.index')
                ->with('success', 'Profil berhasil diperbarui!');
        }
        
        /**
         * Show domain settings page
         */
        public function domain()
        {
            $user = Auth::user();
            $reseller = $user->resellerProfile;
            
            if (!$reseller) {
                return redirect()->route('reseller.setup');
            }
            
            // Check if can use custom domain based on membership level
            $canUseCustomDomain = $reseller->membership_level === 'gold' && $reseller->isActive();
            
            return view('reseller.profile.domain', compact('reseller', 'canUseCustomDomain'));
        }
        
        /**
         * Update subdomain
         */
        public function updateSubdomain(Request $request, DomainService $domainService)
        {
            $user = Auth::user();
            $reseller = $user->resellerProfile;
            
            if (!$reseller) {
                return redirect()->route('reseller.setup');
            }
            
            $request->validate([
                'subdomain' => [
                    'required', 
                    'string', 
                    'min:3', 
                    'max:30', 
                    'regex:/^[a-z0-9\-]+$/',
                    Rule::unique('reseller_profiles')->ignore($reseller->id)
                ],
            ]);
            
            // Format subdomain
            $subdomain = $domainService->formatSubdomain($request->subdomain);
            
            // Cek ketersediaan subdomain
            if (!$domainService->checkSubdomainAvailability($subdomain) && $subdomain !== $reseller->subdomain) {
                return back()->withErrors(['subdomain' => 'Subdomain ini sudah digunakan.'])->withInput();
            }
            
            // Update subdomain
            $reseller->update([
                'subdomain' => $subdomain
            ]);
            
            return redirect()->route('reseller.profile.domain')
                ->with('success', 'Subdomain berhasil diperbarui!');
        }
        
        /**
         * Update custom domain
         */
        public function updateCustomDomain(Request $request, DomainService $domainService)
        {
            $user = Auth::user();
            $reseller = $user->resellerProfile;
            
            if (!$reseller) {
                return redirect()->route('reseller.setup');
            }
            
            // Check if can use custom domain based on membership level
            if ($reseller->membership_level !== 'gold' || !$reseller->isActive()) {
                return back()->withErrors(['custom_domain' => 'Fitur ini hanya tersedia untuk paket Gold yang aktif.']);
            }
            
            $request->validate([
                'custom_domain' => 'required|string|max:255',
            ]);
            
            // Format domain
            $domain = $domainService->formatDomain($request->custom_domain);
            
            // Setup custom domain
            $result = $domainService->setupCustomDomain($reseller, $domain);
            
            return redirect()->route('reseller.profile.domain')
                ->with('success', 'Domain kustom berhasil diatur! Silakan ikuti instruksi pengaturan DNS untuk mengarahkan domain Anda.')
                ->with('dns_instructions', $result['instructions']);
        }
        
        /**
         * Check domain availability
         */
        public function checkDomain(Request $request, DomainService $domainService)
        {
            $request->validate([
                'domain' => 'required|string|max:255',
            ]);
            
            $domain = $domainService->formatDomain($request->domain);
            $isAvailable = $domainService->checkDomainAvailability($domain);
            
            return response()->json([
                'success' => true,
                'is_available' => $isAvailable,
                'domain' => $domain
            ]);
        }
        
        /**
         * Update password
         */
        public function updatePassword(Request $request)
        {
            $request->validate([
                'current_password' => 'required|string|current_password',
                'password' => 'required|string|min:8|confirmed',
            ]);
            
            // Update password
            Auth::user()->update([
                'password' => bcrypt($request->password),
            ]);
            
            return redirect()->route('reseller.profile.index')
                ->with('success', 'Password berhasil diperbarui!');
        }
}
    