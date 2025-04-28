<?php

// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ResellerController as AdminResellerController;
use App\Http\Controllers\Admin\GameController as AdminGameController;
use App\Http\Controllers\Admin\GameServiceController as AdminGameServiceController;
use App\Http\Controllers\Admin\ServiceOptionController as AdminServiceOptionController;
use App\Http\Controllers\Admin\MembershipPackageController as AdminMembershipPackageController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\WithdrawalController as AdminWithdrawalController;

use App\Http\Controllers\Reseller\DashboardController as ResellerDashboardController;
use App\Http\Controllers\Reseller\ProfileController as ResellerProfileController;
use App\Http\Controllers\Reseller\GameController as ResellerGameController;
use App\Http\Controllers\Reseller\ServiceController as ResellerServiceController;
use App\Http\Controllers\Reseller\TransactionController as ResellerTransactionController;
use App\Http\Controllers\Reseller\MembershipController as ResellerMembershipController;
use App\Http\Controllers\Reseller\WithdrawalController as ResellerWithdrawalController;
use App\Http\Controllers\Reseller\ContentController as ResellerContentController;

use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\TransactionController as UserTransactionController;
use App\Http\Controllers\User\ProfileController as UserProfileController;

use App\Http\Controllers\Store\StoreController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Autentikasi
Auth::routes(['verify' => true]);

// Webhook routes for payment gateway
Route::prefix('webhook')->name('webhook.')->group(function () {
    Route::post('/xendit/transaction', [App\Http\Controllers\WebhookController::class, 'xenditTransaction'])->name('xendit.transaction');
    Route::post('/xendit/membership', [App\Http\Controllers\WebhookController::class, 'xenditMembership'])->name('xendit.membership');
});

// Store Routes (Accessible without login)
Route::domain('{domain}')->middleware(['web', 'check.reseller.store'])->group(function () {
    Route::get('/', [StoreController::class, 'index'])->name('store.index');
    Route::get('/about', [StoreController::class, 'about'])->name('store.about');
    Route::get('/track', [StoreController::class, 'track'])->name('store.track');
    Route::post('/track', [StoreController::class, 'track'])->name('store.track.post');
    Route::get('/game/{gameSlug}', [StoreController::class, 'game'])->name('store.game');
    Route::get('/game/{gameSlug}/{serviceSlug}', [StoreController::class, 'service'])->name('store.service');
    Route::get('/checkout/{gameSlug}/{serviceSlug}', [StoreController::class, 'checkout'])->name('store.checkout');
    Route::post('/checkout/{gameSlug}/{serviceSlug}', [StoreController::class, 'processCheckout'])->name('store.checkout.process');
    Route::get('/payment/success/{invoice}/{domain}', [StoreController::class, 'paymentSuccess'])->name('store.payment.success');
    Route::get('/payment/failure/{invoice}/{domain}', [StoreController::class, 'paymentFailure'])->name('store.payment.failure');
    Route::post('/voucher/check', [StoreController::class, 'checkVoucher'])->name('store.voucher.check');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'check.role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Reseller Management
    Route::resource('resellers', AdminResellerController::class);
    Route::post('/resellers/{id}/extend-membership', [AdminResellerController::class, 'extendMembership'])->name('resellers.extend-membership');
    
    // Game Management
    Route::resource('games', AdminGameController::class);
    
    // Game Service Management
    Route::resource('games.services', AdminGameServiceController::class);
    
    // Service Option Management
    Route::resource('games.services.options', AdminServiceOptionController::class);
    Route::post('/games/{game}/services/{service}/options/import', [AdminServiceOptionController::class, 'import'])->name('games.services.options.import');
    Route::get('/options/{serviceId}', [AdminServiceOptionController::class, 'getOptions'])->name('options.get');
    
    // Membership Package Management
    Route::resource('membership-packages', AdminMembershipPackageController::class);
    
    // Transaction Management
    Route::get('/transactions/membership', [AdminTransactionController::class, 'membershipIndex'])->name('transactions.membership.index');
    Route::get('/transactions/membership/{id}', [AdminTransactionController::class, 'membershipShow'])->name('transactions.membership.show');
    
    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{id}', [AdminTransactionController::class, 'show'])->name('transactions.show');
    Route::post('/transactions/{id}/process', [AdminTransactionController::class, 'process'])->name('transactions.process');
    Route::post('/transactions/{id}/update-status', [AdminTransactionController::class, 'updateStatus'])->name('transactions.update-status');
    
    // Withdrawal Management
    Route::get('/withdrawals', [AdminWithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::get('/withdrawals/{id}', [AdminWithdrawalController::class, 'show'])->name('withdrawals.show');
    Route::post('/withdrawals/{id}/approve', [AdminWithdrawalController::class, 'approve'])->name('withdrawals.approve');
    Route::post('/withdrawals/{id}/reject', [AdminWithdrawalController::class, 'reject'])->name('withdrawals.reject');

    // Add to routes/web.php

// Admin helper routes
Route::get('/get-resellers', [AdminResellerController::class, 'getResellers'])->name('get-resellers');
Route::get('/get-games', [AdminGameController::class, 'getGames'])->name('get-games');
Route::get('/get-membership-packages', [AdminMembershipPackageController::class, 'getPackages'])->name('get-membership-packages');

// Admin transaction exports
Route::get('/transactions/export', [AdminTransactionController::class, 'export'])->name('transactions.export');
Route::get('/transactions/membership/export', [AdminTransactionController::class, 'membershipExport'])->name('transactions.membership.export');

// Admin payment check routes
Route::post('/transactions/{id}/check-payment', [AdminTransactionController::class, 'checkPayment'])->name('transactions.check-payment');
Route::post('/transactions/membership/{id}/check-payment', [AdminTransactionController::class, 'checkMembershipPayment'])->name('transactions.membership.check-payment');

// Admin user management (missing route)
Route::resource('/users', \App\Http\Controllers\Admin\UserController::class, ['as' => 'admin']);

// Password reset for resellers
Route::post('/resellers/{id}/reset-password', [AdminResellerController::class, 'resetPassword'])->name('resellers.reset-password');
});

// Reseller Routes
Route::prefix('reseller')->name('reseller.')->middleware(['auth', 'check.role:reseller'])->group(function () {
    Route::get('/dashboard', [ResellerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/setup', [ResellerDashboardController::class, 'setup'])->name('setup');
    Route::post('/setup', [ResellerDashboardController::class, 'storeSetup'])->name('setup.store');
    
    // Profile Management
    Route::get('/profile', [ResellerProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [ResellerProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/domain', [ResellerProfileController::class, 'domain'])->name('profile.domain');
    Route::post('/profile/domain/subdomain', [ResellerProfileController::class, 'updateSubdomain'])->name('profile.domain.subdomain');
    Route::post('/profile/domain/custom', [ResellerProfileController::class, 'updateCustomDomain'])->name('profile.domain.custom');
    Route::post('/profile/domain/check', [ResellerProfileController::class, 'checkDomain'])->name('profile.domain.check');
    Route::post('/profile/password', [ResellerProfileController::class, 'updatePassword'])->name('profile.password');
    
    // Game Management
    Route::get('/games', [ResellerGameController::class, 'index'])->name('games.index');
    Route::get('/games/{id}/add', [ResellerGameController::class, 'add'])->name('games.add');
    Route::post('/games/{id}/store', [ResellerGameController::class, 'store'])->name('games.store');
    Route::get('/games/{id}/edit', [ResellerGameController::class, 'edit'])->name('games.edit');
    Route::put('/games/{id}', [ResellerGameController::class, 'update'])->name('games.update');
    Route::delete('/games/{id}', [ResellerGameController::class, 'destroy'])->name('games.destroy');
    
    // Game Services Management
    Route::get('/games/{id}/services', [ResellerGameController::class, 'services'])->name('games.services');
    Route::get('/games/{gameId}/services/{serviceId}/options', [ResellerServiceController::class, 'options'])->name('services.options');
    Route::post('/games/{gameId}/services/{serviceId}/options', [ResellerServiceController::class, 'updateOptions'])->name('services.options.update');
    Route::post('/games/{gameId}/services/{serviceId}', [ResellerServiceController::class, 'updateService'])->name('services.update');
    
    // Transaction Management
    Route::get('/transactions', [ResellerTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{id}', [ResellerTransactionController::class, 'show'])->name('transactions.show');
    
    // Membership Management
    Route::get('/membership', [ResellerMembershipController::class, 'index'])->name('membership.index');
    Route::get('/membership/{id}/checkout', [ResellerMembershipController::class, 'checkout'])->name('membership.checkout');
    Route::post('/membership/{id}/purchase', [ResellerMembershipController::class, 'purchase'])->name('membership.purchase');
    Route::get('/membership/payment/success/{id}', [ResellerMembershipController::class, 'paymentSuccess'])->name('membership.payment.success');
    Route::get('/membership/payment/failure/{id}', [ResellerMembershipController::class, 'paymentFailure'])->name('membership.payment.failure');
    

    // Withdrawal Management
    Route::get('/withdrawals', [ResellerWithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('/withdrawals', [ResellerWithdrawalController::class, 'store'])->name('withdrawals.store');
    Route::get('/withdrawals/{id}/proof', [ResellerWithdrawalController::class, 'getProof'])->name('withdrawals.proof');
    Route::get('/withdrawals/{id}/reason', [ResellerWithdrawalController::class, 'getRejectionReason'])->name('withdrawals.reason');
    
    // Content Management (Gold Membership Only)
    Route::middleware(['check.gold.membership'])->group(function () {
        Route::get('/content', [ResellerContentController::class, 'index'])->name('content.index');
        Route::post('/content/generate', [ResellerContentController::class, 'generate'])->name('content.generate');
        Route::post('/content/generate-calendar', [ResellerContentController::class, 'generateCalendar'])->name('content.generate-calendar');
        Route::post('/content', [ResellerContentController::class, 'store'])->name('content.store');
        Route::get('/content/{id}', [ResellerContentController::class, 'show'])->name('content.show');
        Route::get('/content/{id}/edit', [ResellerContentController::class, 'edit'])->name('content.edit');
        Route::put('/content/{id}', [ResellerContentController::class, 'update'])->name('content.update');
        Route::delete('/content/{id}', [ResellerContentController::class, 'destroy'])->name('content.destroy');
    });
});
    
    // User Routes
    Route::prefix('user')->name('user.')->middleware(['auth', 'check.role:user'])->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        
        // Transaction Management
        Route::get('/transactions', [UserTransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{id}', [UserTransactionController::class, 'show'])->name('transactions.show');
        Route::get('/track', [UserTransactionController::class, 'trackForm'])->name('transactions.track.form');
        Route::post('/track', [UserTransactionController::class, 'track'])->name('transactions.track');
        
        // Profile Management
        Route::get('/profile', [UserProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile', [UserProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/password', [UserProfileController::class, 'updatePassword'])->name('profile.password');
    });
    
    // Redirect after login based on role
    Route::get('/home', function() {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->role === 'reseller') {
            return redirect()->route('reseller.dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    })->name('dashboard');