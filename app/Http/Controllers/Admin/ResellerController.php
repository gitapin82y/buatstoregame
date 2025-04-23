<?php
// app/Http/Controllers/Admin/ResellerController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ResellerProfile;
use App\Models\MembershipPackage;
use App\Models\MembershipTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ResellerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $resellers = User::with('resellerProfile')
                ->where('role', 'reseller')
                ->get();
                
            return DataTables::of($resellers)
                ->addColumn('status', function($reseller) {
                    if (!$reseller->resellerProfile) {
                        return '<span class="badge bg-warning">Pending</span>';
                    }
                    
                    if ($reseller->resellerProfile->isActive()) {
                        return '<span class="badge bg-success">Active</span>';
                    } elseif ($reseller->resellerProfile->isGracePeriod()) {
                        return '<span class="badge bg-warning">Grace Period</span>';
                    } else {
                        return '<span class="badge bg-danger">Inactive</span>';
                    }
                })
                ->addColumn('store_name', function($reseller) {
                    return $reseller->resellerProfile->store_name ?? '-';
                })
                ->addColumn('membership', function($reseller) {
                    if (!$reseller->resellerProfile) {
                        return '-';
                    }
                    
                    return ucfirst($reseller->resellerProfile->membership_level) . 
                        ' (Expires: ' . ($reseller->resellerProfile->membership_expires_at ? 
                            $reseller->resellerProfile->membership_expires_at->format('d M Y') : 'N/A') . ')';
                })
                ->addColumn('domain', function($reseller) {
                    if (!$reseller->resellerProfile) {
                        return '-';
                    }
                    
                    if ($reseller->resellerProfile->custom_domain) {
                        return '<span class="text-primary">' . $reseller->resellerProfile->custom_domain . '</span>';
                    } elseif ($reseller->resellerProfile->subdomain) {
                        return $reseller->resellerProfile->subdomain . '.buattokogame.com';
                    } else {
                        return '-';
                    }
                })
                ->addColumn('actions', function($reseller) {
                    return '
                        <a href="' . route('admin.resellers.show', $reseller->id) . '" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="' . route('admin.resellers.edit', $reseller->id) . '" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" data-id="' . $reseller->id . '" class="btn btn-sm btn-danger btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['status', 'domain', 'actions'])
                ->make(true);
        }
        
        return view('admin.resellers.index');
    }
    
    /**
     * Show the form for creating a new reseller
     */
    public function create()
    {
        $packages = MembershipPackage::where('status', 'active')->get();
        return view('admin.resellers.create', compact('packages'));
    }
    
    /**
     * Store a newly created reseller
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:20',
            'store_name' => 'required|string|max:255',
            'store_description' => 'nullable|string',
            'subdomain' => 'required|string|min:3|max:30|regex:/^[a-z0-9\-]+$/|unique:reseller_profiles',
            'membership_level' => 'required|in:silver,gold',
            'package_id' => 'required|exists:membership_packages,id',
            'duration_days' => 'required|integer|min:30',
        ]);
        
        // Buat user baru dengan role reseller
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(Str::random(10)), // Password acak, akan diganti saat aktivasi
            'role' => 'reseller',
            'phone_number' => $request->phone_number,
            'status' => 'active',
        ]);
        
        // Buat subdomain
        $subdomain = app(DomainService::class)->formatSubdomain($request->subdomain);
        
        // Buat profil reseller
        $resellerProfile = ResellerProfile::create([
            'user_id' => $user->id,
            'store_name' => $request->store_name,
            'store_description' => $request->store_description,
            'store_theme_color' => '#3490dc', // Default color
            'membership_level' => $request->membership_level,
            'membership_expires_at' => now()->addDays($request->duration_days),
            'subdomain' => $subdomain,
        ]);
        
        // Buat transaksi membership sebagai record
        $package = MembershipPackage::find($request->package_id);
        $transaction = MembershipTransaction::create([
            'reseller_id' => $resellerProfile->id,
            'package_id' => $package->id,
            'invoice_number' => 'MEM' . time() . $user->id,
            'amount' => $package->price,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
        
        // Kirim email welcome & reset password
        $user->sendEmailVerificationNotification();
        $token = app('auth.password.broker')->createToken($user);
        $user->notify(new \App\Notifications\ResellerWelcome($token));
        
        return redirect()->route('admin.resellers.index')
            ->with('success', 'Reseller berhasil dibuat! Email aktivasi telah dikirim.');
    }

    /**
     * Display the specified reseller
     */
    public function show($id)
    {
        $reseller = User::with([
            'resellerProfile', 
            'resellerProfile.resellerGames.game',
            'resellerProfile.transactions',
            'resellerProfile.withdrawals',
            'resellerProfile.membershipTransactions.package'
        ])->findOrFail($id);
        
        if ($reseller->role !== 'reseller') {
            return redirect()->route('admin.resellers.index')
                ->with('error', 'User bukan reseller!');
        }
        
        $profile = $reseller->resellerProfile;
        
        // Stats
        $stats = [
            'total_transactions' => $profile ? $profile->transactions()->count() : 0,
            'total_paid_transactions' => $profile ? $profile->transactions()->where('payment_status', 'paid')->count() : 0,
            'total_sales' => $profile ? $profile->transactions()->where('payment_status', 'paid')->sum('amount') : 0,
            'total_profit' => $profile ? $profile->balance : 0,
            'total_games' => $profile ? $profile->resellerGames()->count() : 0,
            'total_withdrawals' => $profile ? $profile->withdrawals()->count() : 0,
            'pending_withdrawals' => $profile ? $profile->withdrawals()->where('status', 'pending')->count() : 0,
        ];
        
        // Transactions chart data
        $transactionChartData = $profile ? $profile->transactions()
            ->select(
                \DB::raw('DATE(created_at) as date'),
                \DB::raw('COUNT(*) as count'),
                \DB::raw('SUM(CASE WHEN payment_status = "paid" THEN amount ELSE 0 END) as amount')
            )
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date')
            ->get() : collect();
            
        // Latest transactions
        $latestTransactions = $profile ? $profile->transactions()
            ->with(['user', 'game', 'service'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get() : collect();
            
        // Membership history
        $membershipHistory = $profile ? $profile->membershipTransactions()
            ->with('package')
            ->orderByDesc('created_at')
            ->get() : collect();
            
        // Withdrawal history
        $withdrawalHistory = $profile ? $profile->withdrawals()
            ->orderByDesc('created_at')
            ->get() : collect();
        
        return view('admin.resellers.show', compact(
            'reseller', 
            'profile', 
            'stats', 
            'transactionChartData', 
            'latestTransactions',
            'membershipHistory',
            'withdrawalHistory'
        ));
    }

    /**
     * Show the form for editing the specified reseller
     */
    public function edit($id)
    {
        $reseller = User::with('resellerProfile')->findOrFail($id);
        
        if ($reseller->role !== 'reseller') {
            return redirect()->route('admin.resellers.index')
                ->with('error', 'User bukan reseller!');
        }
        
        $packages = MembershipPackage::where('status', 'active')->get();
        
        return view('admin.resellers.edit', compact('reseller', 'packages'));
    }

    /**
     * Update the specified reseller
     */
    public function update(Request $request, $id)
    {
        $reseller = User::findOrFail($id);
        
        if ($reseller->role !== 'reseller') {
            return redirect()->route('admin.resellers.index')
                ->with('error', 'User bukan reseller!');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($reseller->id)],
            'phone_number' => 'required|string|max:20',
            'store_name' => 'required|string|max:255',
            'store_description' => 'nullable|string',
            'subdomain' => [
                'required', 
                'string', 
                'min:3', 
                'max:30', 
                'regex:/^[a-z0-9\-]+$/',
                Rule::unique('reseller_profiles')->ignore($reseller->resellerProfile?->id ?? 0)
            ],
            'custom_domain' => 'nullable|string|max:255',
            'membership_level' => 'required|in:silver,gold',
            'membership_expires_at' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);
        
        // Update user data
        $reseller->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'status' => $request->status,
        ]);
        
        // Update or create reseller profile
        if ($reseller->resellerProfile) {
            $reseller->resellerProfile->update([
                'store_name' => $request->store_name,
                'store_description' => $request->store_description,
                'subdomain' => app(DomainService::class)->formatSubdomain($request->subdomain),
                'custom_domain' => $request->filled('custom_domain') ? 
                    app(DomainService::class)->formatDomain($request->custom_domain) : null,
                'membership_level' => $request->membership_level,
                'membership_expires_at' => $request->membership_expires_at,
            ]);
        } else {
            ResellerProfile::create([
                'user_id' => $reseller->id,
                'store_name' => $request->store_name,
                'store_description' => $request->store_description,
                'store_theme_color' => '#3490dc', // Default color
                'subdomain' => app(DomainService::class)->formatSubdomain($request->subdomain),
                'custom_domain' => $request->filled('custom_domain') ? 
                    app(DomainService::class)->formatDomain($request->custom_domain) : null,
                'membership_level' => $request->membership_level,
                'membership_expires_at' => $request->membership_expires_at,
            ]);
        }
        
        return redirect()->route('admin.resellers.index')
            ->with('success', 'Reseller berhasil diperbarui!');
    }

    /**
     * Remove the specified reseller
     */
    public function destroy($id)
    {
        $reseller = User::findOrFail($id);
        
        if ($reseller->role !== 'reseller') {
            return response()->json([
                'success' => false,
                'message' => 'User bukan reseller!'
            ]);
        }
        
        // Delete user dan cascade ke reseller profile
        $reseller->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Reseller berhasil dihapus!'
        ]);
    }

    /**
     * Extend membership for a reseller
     */
    public function extendMembership(Request $request, $id)
    {
        $reseller = User::with('resellerProfile')->findOrFail($id);
        
        if ($reseller->role !== 'reseller' || !$reseller->resellerProfile) {
            return redirect()->route('admin.resellers.index')
                ->with('error', 'User bukan reseller atau profil tidak ditemukan!');
        }
        
        $request->validate([
            'package_id' => 'required|exists:membership_packages,id',
            'duration_days' => 'required|integer|min:30',
        ]);
        
        $package = MembershipPackage::find($request->package_id);
        $profile = $reseller->resellerProfile;
        
        // Jika reseller masih aktif, tambahkan durasi
        if ($profile->isActive()) {
            $newExpiryDate = $profile->membership_expires_at->addDays($request->duration_days);
        } else {
            $newExpiryDate = now()->addDays($request->duration_days);
        }
        
        // Update membership
        $profile->update([
            'membership_level' => $package->level,
            'membership_expires_at' => $newExpiryDate,
        ]);
        
        // Buat transaksi membership sebagai record
        $transaction = MembershipTransaction::create([
            'reseller_id' => $profile->id,
            'package_id' => $package->id,
            'invoice_number' => 'MEM' . time() . $reseller->id,
            'amount' => $package->price,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
        
        // Kirim email notifikasi
        $reseller->notify(new \App\Notifications\MembershipExtended($profile, $transaction));
        
        return redirect()->route('admin.resellers.show', $reseller->id)
            ->with('success', 'Membership reseller berhasil diperpanjang!');
    }
}
