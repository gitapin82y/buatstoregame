<?php
// app/Http/Controllers/Reseller/ServiceController.php
namespace App\Http\Controllers\Reseller;
    
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameService;
use App\Models\ResellerGame;
use App\Models\ResellerGameService;
use App\Models\ServiceOption;
use App\Models\ResellerServiceOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ServiceController extends Controller
{
    /**
     * Show service options for a game service
     */
    public function options($gameId, $serviceId)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        $game = Game::findOrFail($gameId);
        $service = GameService::findOrFail($serviceId);
        
        // Get reseller game
        $resellerGame = ResellerGame::where('reseller_id', $reseller->id)
            ->where('game_id', $game->id)
            ->firstOrFail();
            
        // Get reseller game service
        $resellerGameService = ResellerGameService::where('reseller_game_id', $resellerGame->id)
            ->where('game_service_id', $service->id)
            ->firstOrFail();
            
        // Get options
        $options = ServiceOption::where('game_service_id', $service->id)
            ->where('status', 'active')
            ->with(['resellerServiceOptions' => function($query) use ($resellerGameService) {
                $query->where('reseller_game_service_id', $resellerGameService->id);
            }])
            ->get();
            
        return view('reseller.services.options', compact('game', 'service', 'resellerGame', 'resellerGameService', 'options'));
    }
    
    /**
     * Update service options
     */
    public function updateOptions(Request $request, $gameId, $serviceId)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        $game = Game::findOrFail($gameId);
        $service = GameService::findOrFail($serviceId);
        
        // Get reseller game
        $resellerGame = ResellerGame::where('reseller_id', $reseller->id)
            ->where('game_id', $game->id)
            ->firstOrFail();
            
        // Get reseller game service
        $resellerGameService = ResellerGameService::where('reseller_game_id', $resellerGame->id)
            ->where('game_service_id', $service->id)
            ->firstOrFail();
            
        $request->validate([
            'options' => 'required|array',
            'options.*.id' => 'required|exists:service_options,id',
            'options.*.selling_price' => 'required|numeric|min:0',
            'options.*.is_active' => 'boolean',
        ]);
        
        foreach ($request->options as $optionData) {
            $option = ServiceOption::findOrFail($optionData['id']);
            
            // Get or create reseller service option
            $resellerOption = ResellerServiceOption::firstOrNew([
                'reseller_game_service_id' => $resellerGameService->id,
                'service_option_id' => $option->id,
            ]);
            
            $resellerOption->selling_price = $optionData['selling_price'];
            $resellerOption->is_active = isset($optionData['is_active']);
            $resellerOption->save();
        }
        
        return redirect()->route('reseller.games.services', $gameId)
            ->with('success', 'Opsi layanan berhasil diperbarui!');
    }
    
    /**
     * Update service settings
     */
    public function updateService(Request $request, $gameId, $serviceId)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        $game = Game::findOrFail($gameId);
        $service = GameService::findOrFail($serviceId);
        
        // Get reseller game
        $resellerGame = ResellerGame::where('reseller_id', $reseller->id)
            ->where('game_id', $game->id)
            ->firstOrFail();
            
        // Get reseller game service
        $resellerGameService = ResellerGameService::where('reseller_game_id', $resellerGame->id)
            ->where('game_service_id', $service->id)
            ->firstOrFail();
            
        $request->validate([
            'is_active' => 'boolean',
            'profit_margin' => 'required|numeric|min:0|max:100',
            'display_order' => 'required|integer|min:0',
            'apply_to_all' => 'boolean',
        ]);
        
        // Update reseller game service
        $resellerGameService->update([
            'is_active' => $request->has('is_active'),
            'profit_margin' => $request->profit_margin,
            'display_order' => $request->display_order,
        ]);
        
        // Apply profit margin to all options if requested
        if ($request->has('apply_to_all')) {
            $options = ServiceOption::where('game_service_id', $service->id)
                ->where('status', 'active')
                ->get();
                
            foreach ($options as $option) {
                $sellingPrice = $option->base_price * (1 + ($request->profit_margin / 100));
                
                $resellerOption = ResellerServiceOption::firstOrNew([
                    'reseller_game_service_id' => $resellerGameService->id,
                    'service_option_id' => $option->id,
                ]);
                
                $resellerOption->selling_price = $sellingPrice;
                $resellerOption->save();
            }
        }
        
        return redirect()->route('reseller.games.services', $gameId)
            ->with('success', 'Layanan berhasil diperbarui!');
    }
}