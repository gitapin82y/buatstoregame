<?php
// app/Http/Controllers/Admin/ServiceOptionController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameService;
use App\Models\ServiceOption;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ServiceOptionController extends Controller
{
    /**
     * Display a listing of the options for a service
     */
    public function index(Request $request, $gameId, $serviceId)
    {
        $game = Game::findOrFail($gameId);
        $service = GameService::findOrFail($serviceId);
        
        if ($request->ajax()) {
            $options = ServiceOption::where('game_service_id', $serviceId);
                
            return DataTables::of($options)
                ->addColumn('base_price_formatted', function($option) {
                    return 'Rp ' . number_format($option->base_price, 0, ',', '.');
                })
                ->addColumn('status_badge', function($option) {
                    if ($option->status === 'active') {
                        return '<span class="badge bg-success">Active</span>';
                    } else {
                        return '<span class="badge bg-danger">Inactive</span>';
                    }
                })
                ->addColumn('actions', function($option) use ($gameId, $serviceId) {
                    return '
                        <a href="' . route('admin.games.services.options.edit', ['game' => $gameId, 'service' => $serviceId, 'option' => $option->id]) . '" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" data-id="' . $option->id . '" class="btn btn-sm btn-danger btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }
        
        return view('admin.games.services.options.index', compact('game', 'service'));
    }
    
    /**
     * Show the form for creating a new option
     */
    public function create($gameId, $serviceId)
    {
        $game = Game::findOrFail($gameId);
        $service = GameService::findOrFail($serviceId);
        
        return view('admin.games.services.options.create', compact('game', 'service'));
    }
    
    /**
     * Store a newly created option
     */
    public function store(Request $request, $gameId, $serviceId)
    {
        $game = Game::findOrFail($gameId);
        $service = GameService::findOrFail($serviceId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'api_code' => 'nullable|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);
        
        // Create option
        $option = ServiceOption::create([
            'game_service_id' => $serviceId,
            'name' => $request->name,
            'description' => $request->description,
            'api_code' => $request->api_code,
            'base_price' => $request->base_price,
            'status' => $request->status,
        ]);
        
        return redirect()->route('admin.games.services.options.index', ['game' => $gameId, 'service' => $serviceId])
            ->with('success', 'Opsi layanan berhasil ditambahkan!');
    }
    
    public function edit($gameId, $serviceId, $optionId)
    {
        $game = Game::findOrFail($gameId);
        $service = GameService::findOrFail($serviceId);
        $option = ServiceOption::findOrFail($optionId);
        
        return view('admin.games.services.options.edit', compact('game', 'service', 'option'));
    }
    
    /**
     * Update the specified option
     */
    public function update(Request $request, $gameId, $serviceId, $optionId)
    {
        $game = Game::findOrFail($gameId);
        $service = GameService::findOrFail($serviceId);
        $option = ServiceOption::findOrFail($optionId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'api_code' => 'nullable|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);
        
        // Update option
        $option->update([
            'name' => $request->name,
            'description' => $request->description,
            'api_code' => $request->api_code,
            'base_price' => $request->base_price,
            'status' => $request->status,
        ]);
        
        return redirect()->route('admin.games.services.options.index', ['game' => $gameId, 'service' => $serviceId])
            ->with('success', 'Opsi layanan berhasil diperbarui!');
    }
    
    /**
     * Remove the specified option
     */
    public function destroy($gameId, $serviceId, $optionId)
    {
        $option = ServiceOption::findOrFail($optionId);
        
        // Delete option
        $option->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Opsi layanan berhasil dihapus!'
        ]);
    }
    
    /**
     * Bulk import options
     */
    public function import(Request $request, $gameId, $serviceId)
    {
        $game = Game::findOrFail($gameId);
        $service = GameService::findOrFail($serviceId);
        
        $request->validate([
            'options_json' => 'required|json',
        ]);
        
        $options = json_decode($request->options_json, true);
        $count = 0;
        
        foreach ($options as $optionData) {
            if (isset($optionData['name']) && isset($optionData['base_price'])) {
                ServiceOption::create([
                    'game_service_id' => $serviceId,
                    'name' => $optionData['name'],
                    'description' => $optionData['description'] ?? null,
                    'api_code' => $optionData['api_code'] ?? null,
                    'base_price' => $optionData['base_price'],
                    'status' => $optionData['status'] ?? 'active',
                ]);
                
                $count++;
            }
        }
        
        return redirect()->route('admin.games.services.options.index', ['game' => $gameId, 'service' => $serviceId])
            ->with('success', $count . ' opsi layanan berhasil diimpor!');
    }
    
    /**
     * Get options for API
     */
    public function getOptions($serviceId)
    {
        $options = ServiceOption::where('game_service_id', $serviceId)
            ->where('status', 'active')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $options
        ]);
    }
}