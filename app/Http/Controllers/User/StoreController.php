<?php

// app/Http/Controllers/Store/StoreController.php
namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\ResellerProfile;
use App\Models\ResellerGame;
use App\Models\Game;
use App\Models\GameService;
use App\Models\ServiceOption;
use App\Models\ResellerGameService;
use App\Models\ResellerServiceOption;
use App\Models\UserTransaction;
use App\Models\Voucher;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    /**
     * Show store homepage
     */
    public function index($domain)
    {
        // Find reseller by subdomain or custom domain
        $reseller = ResellerProfile::where('subdomain', $domain)
            ->orWhere('custom_domain', $domain)
            ->firstOrFail();
            
        // Check if membership is active
        if (!$reseller->isActive() && !$reseller->isGracePeriod()) {
            return view('store.inactive', compact('reseller'));
        }
        
        // Get active games for this reseller
        $games = Game::whereHas('resellerGames', function($query) use ($reseller) {
            $query->where('reseller_id', $reseller->id)
                ->where('is_active', true);
        })->get();
        
        // Get featured games (first 4)
        $featuredGames = $games->take(4);
        
        return view('store.index', compact('reseller', 'games', 'featuredGames'));
    }
    
    /**
     * Show game details page
     */
    public function game($domain, $gameSlug)
    {
        // Find reseller by subdomain or custom domain
        $reseller = ResellerProfile::where('subdomain', $domain)
            ->orWhere('custom_domain', $domain)
            ->firstOrFail();
            
        // Check if membership is active
        if (!$reseller->isActive()) {
            return view('store.inactive', compact('reseller'));
        }
        
        // Find game
        $game = Game::where('slug', $gameSlug)->firstOrFail();
        
        // Get reseller game
        $resellerGame = ResellerGame::where('reseller_id', $reseller->id)
            ->where('game_id', $game->id)
            ->where('is_active', true)
            ->firstOrFail();
            
        // Get active services for this game
        $services = GameService::where('game_id', $game->id)
            ->whereHas('resellerGameServices', function($query) use ($resellerGame) {
                $query->where('reseller_game_id', $resellerGame->id)
                    ->where('is_active', true);
            })
            ->with(['resellerGameServices' => function($query) use ($resellerGame) {
                $query->where('reseller_game_id', $resellerGame->id);
            }])
            ->get();
            
        return view('store.game', compact('reseller', 'game', 'resellerGame', 'services'));
    }
    
    /**
     * Show service details page
     */
    public function service($domain, $gameSlug, $serviceSlug)
    {
        // Find reseller by subdomain or custom domain
        $reseller = ResellerProfile::where('subdomain', $domain)
            ->orWhere('custom_domain', $domain)
            ->firstOrFail();
            
        // Check if membership is active
        if (!$reseller->isActive()) {
            return view('store.inactive', compact('reseller'));
        }
        
        // Find game
        $game = Game::where('slug', $gameSlug)->firstOrFail();
        
        // Get reseller game
        $resellerGame = ResellerGame::where('reseller_id', $reseller->id)
            ->where('game_id', $game->id)
            ->where('is_active', true)
            ->firstOrFail();
            
        // Find service
        $service = GameService::where('game_id', $game->id)
            ->where('slug', $serviceSlug)
            ->firstOrFail();
            
        // Get reseller game service
        $resellerGameService = ResellerGameService::where('reseller_game_id', $resellerGame->id)
            ->where('game_service_id', $service->id)
            ->where('is_active', true)
            ->firstOrFail();
            
        // Get active options for this service
        $options = ServiceOption::where('game_service_id', $service->id)
            ->where('status', 'active')
            ->whereHas('resellerServiceOptions', function($query) use ($resellerGameService) {
                $query->where('reseller_game_service_id', $resellerGameService->id)
                    ->where('is_active', true);
            })
            ->with(['resellerServiceOptions' => function($query) use ($resellerGameService) {
                $query->where('reseller_game_service_id', $resellerGameService->id);
            }])
            ->get();
            
        return view('store.service', compact('reseller', 'game', 'service', 'options'));
    }
    
    /**
     * Show checkout page
     */
    public function checkout(Request $request, $domain, $gameSlug, $serviceSlug)
    {
        // Find reseller by subdomain or custom domain
        $reseller = ResellerProfile::where('subdomain', $domain)
            ->orWhere('custom_domain', $domain)
            ->firstOrFail();
            
        // Check if membership is active
        if (!$reseller->isActive()) {
            return view('store.inactive', compact('reseller'));
        }
        
        // Find game
        $game = Game::where('slug', $gameSlug)->firstOrFail();
        
        // Get reseller game
        $resellerGame = ResellerGame::where('reseller_id', $reseller->id)
            ->where('game_id', $game->id)
            ->where('is_active', true)
            ->firstOrFail();
            
        // Find service
        $service = GameService::where('game_id', $game->id)
            ->where('slug', $serviceSlug)
            ->firstOrFail();
            
        // Get reseller game service
        $resellerGameService = ResellerGameService::where('reseller_game_id', $resellerGame->id)
            ->where('game_service_id', $service->id)
            ->where('is_active', true)
            ->firstOrFail();
            
        // Validate option ID
        $request->validate([
            'option_id' => 'required|exists:service_options,id',
        ]);
        
        // Get option
        $option = ServiceOption::findOrFail($request->option_id);
        
        // Get reseller service option
        $resellerOption = ResellerServiceOption::where('reseller_game_service_id', $resellerGameService->id)
            ->where('service_option_id', $option->id)
            ->where('is_active', true)
            ->firstOrFail();
            
        // Check if user logged in
        $user = Auth::user();
        
        return view('store.checkout', compact(
            'reseller', 
            'game', 
            'service', 
            'option', 
            'resellerOption', 
            'user'
        ));
    }
    
    /**
     * Process checkout
     */
    public function processCheckout(Request $request, $domain, $gameSlug, $serviceSlug, PaymentService $paymentService)
    {
        // Find reseller by subdomain or custom domain
        $reseller = ResellerProfile::where('subdomain', $domain)
            ->orWhere('custom_domain', $domain)
            ->firstOrFail();
            
        // Check if membership is active
        if (!$reseller->isActive()) {
            return view('store.inactive', compact('reseller'));
        }
        
        // Find game
        $game = Game::where('slug', $gameSlug)->firstOrFail();
        
        // Get reseller game
        $resellerGame = ResellerGame::where('reseller_id', $reseller->id)
            ->where('game_id', $game->id)
            ->where('is_active', true)
            ->firstOrFail();
            
        // Find service
        $service = GameService::where('game_id', $game->id)
            ->where('slug', $serviceSlug)
            ->firstOrFail();
            
        // Get reseller game service
        $resellerGameService = ResellerGameService::where('reseller_game_id', $resellerGame->id)
            ->where('game_service_id', $service->id)
            ->where('is_active', true)
            ->firstOrFail();
            
        // Validate request
        $validationRules = [
            'option_id' => 'required|exists:service_options,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
        ];
        
        // Add game-specific fields validation
        if ($service->type === 'topup') {
            $validationRules['user_id'] = 'required|string|max:50';
            $validationRules['server_id'] = 'nullable|string|max:50';
        } elseif ($service->type === 'joki') {
            $validationRules['user_id'] = 'required|string|max:50';
            $validationRules['password'] = 'required|string|max:50';
            $validationRules['notes'] = 'nullable|string';
        } elseif ($service->type === 'coaching' || $service->type === 'formation') {
            $validationRules['user_id'] = 'required|string|max:50';
            $validationRules['notes'] = 'nullable|string';
        }
        
        // Validate voucher if provided
        $validationRules['voucher_code'] = 'nullable|string|max:50';
        
        $request->validate($validationRules);
        
        // Get option
        $option = ServiceOption::findOrFail($request->option_id);
        
        // Get reseller service option
        $resellerOption = ResellerServiceOption::where('reseller_game_service_id', $resellerGameService->id)
            ->where('service_option_id', $option->id)
            ->where('is_active', true)
            ->firstOrFail();
            
        // Check if user logged in, if not create guest user
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            // Check if user with this email already exists
            $existingUser = \App\Models\User::where('email', $request->email)->first();
            
            if ($existingUser) {
                $user = $existingUser;
            } else {
                // Create new user with random password
                $user = \App\Models\User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make(Str::random(10)),
                    'role' => 'user',
                    'phone_number' => $request->phone,
                ]);
            }
        }
        
        // Calculate amount
        $amount = $resellerOption->selling_price;
        
        // Apply voucher if provided
        $discount = 0;
        if ($request->filled('voucher_code')) {
            $voucher = Voucher::where('reseller_id', $reseller->id)
                ->where('code', $request->voucher_code)
                ->where('status', 'active')
                ->first();
                
            if ($voucher && $voucher->isValid() && $amount >= $voucher->min_purchase) {
                if ($voucher->discount_type === 'percentage') {
                    $discount = $amount * ($voucher->discount_amount / 100);
                    if ($voucher->max_discount && $discount > $voucher->max_discount) {
                        $discount = $voucher->max_discount;
                    }
                } else {
                    $discount = $voucher->discount_amount;
                }
                
                // Increment voucher used count
                $voucher->increment('used_count');
            }
        }
        
        // Apply discount
        $finalAmount = $amount - $discount;
        
        // Generate invoice number
        $invoiceNumber = 'INV-' . time() . '-' . $user->id;
        
        // Create transaction
        $transaction = UserTransaction::create([
            'user_id' => $user->id,
            'reseller_id' => $reseller->id,
            'game_id' => $game->id,
            'service_id' => $service->id,
            'option_id' => $option->id,
            'invoice_number' => $invoiceNumber,
            'user_identifier' => $request->user_id,
            'server_identifier' => $request->server_id ?? null,
            'amount' => $finalAmount,
            'payment_status' => 'pending',
            'process_status' => 'pending',
            'expired_at' => now()->addDay(),
        ]);
        
        // Store additional data based on service type
        if ($service->type === 'joki') {
            // Store password securely
            $transaction->notes = json_encode([
                'password' => encrypt($request->password),
                'notes' => $request->notes ?? '',
            ]);
            $transaction->save();
        } elseif ($service->type === 'coaching' || $service->type === 'formation') {
            $transaction->notes = $request->notes ?? '';
            $transaction->save();
        }
        
        // Create payment
        try {
            $paymentUrl = $paymentService->createUserInvoice($transaction);
            
            return redirect()->to($paymentUrl);
        } catch (\Exception $e) {
            return redirect()->route('store.service', ['domain' => $domain, 'gameSlug' => $gameSlug, 'serviceSlug' => $serviceSlug])
                ->with('error', 'Terjadi kesalahan saat membuat pembayaran: ' . $e->getMessage());
        }
    }
    
    /**
     * Payment success
     */
    public function paymentSuccess(Request $request, $invoice, $domain)
    {
        $transaction = UserTransaction::where('invoice_number', $invoice)->firstOrFail();
        $reseller = ResellerProfile::where('subdomain', $domain)
            ->orWhere('custom_domain', $domain)
            ->firstOrFail();
            
        return view('store.payment-success', compact('transaction', 'reseller'));
    }
    
    /**
     * Payment failure
     */
    public function paymentFailure(Request $request, $invoice, $domain)
    {
        $transaction = UserTransaction::where('invoice_number', $invoice)->firstOrFail();
        $reseller = ResellerProfile::where('subdomain', $domain)
            ->orWhere('custom_domain', $domain)
            ->firstOrFail();
            
        return view('store.payment-failure', compact('transaction', 'reseller'));
    }
    
    /**
     * Check voucher validity
     */
    public function checkVoucher(Request $request, $domain)
    {
        // Find reseller by subdomain or custom domain
        $reseller = ResellerProfile::where('subdomain', $domain)
            ->orWhere('custom_domain', $domain)
            ->firstOrFail();
            
        $request->validate([
            'code' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
        ]);
        
        // Find voucher
        $voucher = Voucher::where('reseller_id', $reseller->id)
            ->where('code', $request->code)
            ->where('status', 'active')
            ->first();
            
        if (!$voucher || !$voucher->isValid() || $request->amount < $voucher->min_purchase) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher tidak valid atau tidak dapat digunakan.'
            ]);
        }
        
        // Calculate discount
        $discount = 0;
        if ($voucher->discount_type === 'percentage') {
            $discount = $request->amount * ($voucher->discount_amount / 100);
            if ($voucher->max_discount && $discount > $voucher->max_discount) {
                $discount = $voucher->max_discount;
            }
        } else {
            $discount = $voucher->discount_amount;
        }
        
        return response()->json([
            'success' => true,
            'discount' => $discount,
            'final_amount' => $request->amount - $discount,
            'message' => 'Voucher berhasil diterapkan!'
        ]);
    }
    
    /**
     * About store page
     */
    public function about($domain)
    {
        // Find reseller by subdomain or custom domain
        $reseller = ResellerProfile::where('subdomain', $domain)
            ->orWhere('custom_domain', $domain)
            ->firstOrFail();
            
        // Check if membership is active
        if (!$reseller->isActive() && !$reseller->isGracePeriod()) {
            return view('store.inactive', compact('reseller'));
        }
        
        return view('store.about', compact('reseller'));
    }
    
    /**
     * Track order
     */
    public function track(Request $request, $domain)
    {
        // Find reseller by subdomain or custom domain
        $reseller = ResellerProfile::where('subdomain', $domain)
            ->orWhere('custom_domain', $domain)
            ->firstOrFail();
            
        // Check if membership is active
        if (!$reseller->isActive() && !$reseller->isGracePeriod()) {
            return view('store.inactive', compact('reseller'));
        }
        
        if ($request->isMethod('post')) {
            $request->validate([
                'invoice_number' => 'required|string',
            ]);
            
            $transaction = UserTransaction::with([
                'game', 
                'service', 
                'option'
            ])
            ->where('invoice_number', $request->invoice_number)
            ->where('reseller_id', $reseller->id)
            ->first();
            
            if (!$transaction) {
                return back()->with('error', 'Invoice tidak ditemukan.');
            }
            
            return view('store.track-result', compact('reseller', 'transaction'));
        }
        
        return view('store.track', compact('reseller'));
    }
}