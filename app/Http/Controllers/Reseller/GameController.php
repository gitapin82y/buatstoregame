<?php
// app/Http/Controllers/Reseller/GameController.php
namespace App\Http\Controllers\Reseller;
    
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameService;
use App\Models\ResellerGame;
use App\Models\ResellerGameService;
use App\Models\ResellerServiceOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class GameController extends Controller
{
    /**
     * Display a listing of the games
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        if ($request->ajax()) {
            $games = Game::with(['resellerGames' => function($query) use ($reseller) {
                $query->where('reseller_id', $reseller->id);
            }])->where('status', 'active');
                
            return DataTables::of($games)
                ->addColumn('is_active', function($game) {
                    $resellerGame = $game->resellerGames->first();
                    return $resellerGame ? $resellerGame->is_active : false;
                })
                ->addColumn('status_badge', function($game) {
                    $resellerGame = $game->resellerGames->first();
                    if ($resellerGame && $resellerGame->is_active) {
                        return '<span class="badge bg-success">Active</span>';
                    } else {
                        return '<span class="badge bg-secondary">Inactive</span>';
                    }
                })
                ->addColumn('profit_margin', function($game) {
                    $resellerGame = $game->resellerGames->first();
                    return $resellerGame ? $resellerGame->profit_margin . '%' : '0%';
                })
                ->addColumn('actions', function($game) {
                    $resellerGame = $game->resellerGames->first();
                    
                    if ($resellerGame) {
                        return '
                            <a href="' . route('reseller.games.edit', $game->id) . '" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="' . route('reseller.games.services', $game->id) . '" class="btn btn-sm btn-info">
                                <i class="fas fa-cogs"></i> Services
                            </a>
                        ';
                    } else {
                        return '
                            <a href="' . route('reseller.games.add', $game->id) . '" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> Add to Store
                            </a>
                        ';
                    }
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }
        
        return view('reseller.games.index');
    }
    
    /**
     * Add game to reseller store
     */
    public function add($id)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        $game = Game::findOrFail($id);
        
        // Check if game already added
        $existing = ResellerGame::where('reseller_id', $reseller->id)
            ->where('game_id', $game->id)
            ->first();
            
        if ($existing) {
            return redirect()->route('reseller.games.edit', $game->id);
        }
        
        return view('reseller.games.add', compact('game'));
    }
    
    /**
     * Store game to reseller
     */
    public function store(Request $request, $id)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        $game = Game::findOrFail($id);
        
        // Check if game already added
        $existing = ResellerGame::where('reseller_id', $reseller->id)
            ->where('game_id', $game->id)
            ->first();
            
        if ($existing) {
            return redirect()->route('reseller.games.edit', $game->id);
        }
        
        $request->validate([
            'is_active' => 'boolean',
            'profit_margin' => 'required|numeric|min:0|max:100',
            'display_order' => 'required|integer|min:0',
        ]);
        
        // Create reseller game
        $resellerGame = ResellerGame::create([
            'reseller_id' => $reseller->id,
            'game_id' => $game->id,
            'is_active' => $request->has('is_active'),
            'profit_margin' => $request->profit_margin,
            'display_order' => $request->display_order,
        ]);
        
        // Add all active services of the game
        $services = GameService::where('game_id', $game->id)
            ->where('status', 'active')
            ->get();
            
        foreach ($services as $service) {
            $resellerGameService = ResellerGameService::create([
                'reseller_game_id' => $resellerGame->id,
                'game_service_id' => $service->id,
                'profit_margin' => $request->profit_margin,
                'is_active' => true,
                'display_order' => 0,
            ]);
            
            // Add all options for this service
            foreach ($service->options as $option) {
                if ($option->status === 'active') {
                    $sellingPrice = $option->base_price * (1 + ($request->profit_margin / 100));
                    
                    ResellerServiceOption::create([
                        'reseller_game_service_id' => $resellerGameService->id,
                        'service_option_id' => $option->id,
                        'selling_price' => $sellingPrice,
                        'is_active' => true,
                    ]);
                }
            }
        }
        
        return redirect()->route('reseller.games.services', $game->id)
            ->with('success', 'Game berhasil ditambahkan ke toko Anda! Silakan atur layanan dan harga.');
    }
    
    /**
     * Show the form for editing the specified game
     */
    public function edit($id)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        $game = Game::findOrFail($id);
        
        // Get reseller game
        $resellerGame = ResellerGame::where('reseller_id', $reseller->id)
            ->where('game_id', $game->id)
            ->firstOrFail();
            
        return view('reseller.games.edit', compact('game', 'resellerGame'));
    }
    
    /**
     * Update the specified game
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        $game = Game::findOrFail($id);
        
        // Get reseller game
        $resellerGame = ResellerGame::where('reseller_id', $reseller->id)
            ->where('game_id', $game->id)
            ->firstOrFail();
            
        $request->validate([
            'is_active' => 'boolean',
            'profit_margin' => 'required|numeric|min:0|max:100',
            'display_order' => 'required|integer|min:0',
        ]);
        
        // Update reseller game
        $resellerGame->update([
            'is_active' => $request->has('is_active'),
            'profit_margin' => $request->profit_margin,
            'display_order' => $request->display_order,
        ]);
        
        return redirect()->route('reseller.games.index')
            ->with('success', 'Game berhasil diperbarui!');
    }
    
    /**
     * Remove the game from reseller store
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        $game = Game::findOrFail($id);
        
        // Get reseller game
        $resellerGame = ResellerGame::where('reseller_id', $reseller->id)
            ->where('game_id', $game->id)
            ->firstOrFail();
            
        // Delete reseller game
        $resellerGame->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Game berhasil dihapus dari toko Anda!'
        ]);
    }
    
    /**
     * Show services for a game
     */
    public function services($id)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        $game = Game::findOrFail($id);
        
        // Get reseller game
        $resellerGame = ResellerGame::where('reseller_id', $reseller->id)
            ->where('game_id', $game->id)
            ->firstOrFail();
            
        // Get services
        $services = GameService::where('game_id', $game->id)
            ->where('status', 'active')
            ->with(['resellerGameServices' => function($query) use ($resellerGame) {
                $query->where('reseller_game_id', $resellerGame->id);
            }])
            ->get();
            
        return view('reseller.games.services', compact('game', 'resellerGame', 'services'));
    }
}