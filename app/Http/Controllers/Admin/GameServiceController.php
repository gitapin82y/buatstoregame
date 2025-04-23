<?php

    // app/Http/Controllers/Admin/GameServiceController.php
    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use App\Models\Game;
    use App\Models\GameService;
    use App\Models\ServiceOption;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Yajra\DataTables\Facades\DataTables;

    class GameServiceController extends Controller
{
    /**
     * Display a listing of the services for a game
     */
    public function index(Request $request, $gameId)
    {
        $game = Game::findOrFail($gameId);
        
        if ($request->ajax()) {
            $services = GameService::where('game_id', $gameId)
                ->withCount('options');
                
            return DataTables::of($services)
                ->addColumn('image', function($service) {
                    if ($service->image) {
                        return '<img src="' . Storage::disk('public')->url($service->image) . '" alt="' . $service->name . '" class="img-thumbnail" width="50">';
                    }
                    return '<span class="badge bg-secondary">No Image</span>';
                })
                ->addColumn('status_badge', function($service) {
                    if ($service->status === 'active') {
                        return '<span class="badge bg-success">Active</span>';
                    } else {
                        return '<span class="badge bg-danger">Inactive</span>';
                    }
                })
                ->addColumn('actions', function($service) use ($gameId) {
                    return '
                        <a href="' . route('admin.games.services.show', ['game' => $gameId, 'service' => $service->id]) . '" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="' . route('admin.games.services.edit', ['game' => $gameId, 'service' => $service->id]) . '" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" data-id="' . $service->id . '" class="btn btn-sm btn-danger btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['image', 'status_badge', 'actions'])
                ->make(true);
        }
        
        return view('admin.games.services.index', compact('game'));
    }

    /**
     * Show the form for creating a new service
     */
    public function create($gameId)
    {
        $game = Game::findOrFail($gameId);
        return view('admin.games.services.create', compact('game'));
    }

    /**
     * Store a newly created service
     */
    public function store(Request $request, $gameId)
    {
        $game = Game::findOrFail($gameId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:topup,joki,coaching,formation',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price_range' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);
        
        // Generate slug
        $slug = Str::slug($request->name);
        $count = GameService::where('game_id', $gameId)
            ->where('slug', 'like', $slug . '%')
            ->count();
            
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }
        
        // Upload image jika ada
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('games/services', 'public');
        }
        
        // Create service
        $service = GameService::create([
            'game_id' => $gameId,
            'name' => $request->name,
            'slug' => $slug,
            'type' => $request->type,
            'description' => $request->description,
            'image' => $imagePath,
            'price_range' => $request->price_range,
            'status' => $request->status,
        ]);
        
        return redirect()->route('admin.games.services.index', $gameId)
            ->with('success', 'Layanan game berhasil ditambahkan!');
    }

    /**
     * Display the specified service
     */
    public function show($gameId, $serviceId)
    {
        $game = Game::findOrFail($gameId);
        $service = GameService::with('options')->findOrFail($serviceId);
        
        return view('admin.games.services.show', compact('game', 'service'));
    }
    
    /**
     * Show the form for editing the specified service
     */
    public function edit($gameId, $serviceId)
    {
        $game = Game::findOrFail($gameId);
        $service = GameService::findOrFail($serviceId);
        
        return view('admin.games.services.edit', compact('game', 'service'));
    }
    
    /**
     * Update the specified service
     */
    public function update(Request $request, $gameId, $serviceId)
    {
        $game = Game::findOrFail($gameId);
        $service = GameService::findOrFail($serviceId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:topup,joki,coaching,formation',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price_range' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);
        
        // Upload image jika ada
        if ($request->hasFile('image')) {
            // Hapus image lama jika ada
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            
            $imagePath = $request->file('image')->store('games/services', 'public');
            $service->image = $imagePath;
        }
        
        // Update service
        $service->update([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'price_range' => $request->price_range,
            'status' => $request->status,
        ]);
        
        return redirect()->route('admin.games.services.index', $gameId)
            ->with('success', 'Layanan game berhasil diperbarui!');
    }
    
    /**
     * Remove the specified service
     */
    public function destroy($gameId, $serviceId)
    {
        $service = GameService::findOrFail($serviceId);
        
        // Hapus file terkait
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }
        
        // Delete service
        $service->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Layanan game berhasil dihapus!'
        ]);
    }
}

