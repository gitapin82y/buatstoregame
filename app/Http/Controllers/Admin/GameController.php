<?php

    // app/Http/Controllers/Admin/GameController.php
    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use App\Models\Game;
    use App\Models\GameService;
    use App\Models\ServiceOption;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Yajra\DataTables\Facades\DataTables;

    class GameController extends Controller
{
    /**
     * Display a listing of the games
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $games = Game::withCount(['services', 'resellerGames']);
                
            return DataTables::of($games)
                ->addColumn('logo', function($game) {
                    if ($game->logo) {
                        return '<img src="' . Storage::disk('public')->url($game->logo) . '" alt="' . $game->name . '" class="img-thumbnail" width="50">';
                    }
                    return '<span class="badge bg-secondary">No Logo</span>';
                })
                ->addColumn('status_badge', function($game) {
                    if ($game->status === 'active') {
                        return '<span class="badge bg-success">Active</span>';
                    } else {
                        return '<span class="badge bg-danger">Inactive</span>';
                    }
                })
                ->addColumn('actions', function($game) {
                    return '
                        <a href="' . route('admin.games.show', $game->id) . '" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="' . route('admin.games.edit', $game->id) . '" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" data-id="' . $game->id . '" class="btn btn-sm btn-danger btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['logo', 'status_badge', 'actions'])
                ->make(true);
        }
        
        return view('admin.games.index');
    }

    /**
     * Show the form for creating a new game
     */
    public function create()
    {
        return view('admin.games.create');
    }

    /**
     * Store a newly created game
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);
        
        // Generate slug
        $slug = Str::slug($request->name);
        $count = Game::where('slug', 'like', $slug . '%')->count();
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }
        
        // Upload logo jika ada
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('games/logos', 'public');
        }
        
        // Upload banner jika ada
        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('games/banners', 'public');
        }
        
        // Create game
        $game = Game::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'logo' => $logoPath,
            'banner' => $bannerPath,
            'status' => $request->status,
        ]);
        
        return redirect()->route('admin.games.index')
            ->with('success', 'Game berhasil ditambahkan!');
    }

    /**
     * Display the specified game
     */
    public function show($id)
    {
        $game = Game::with([
            'services' => function($query) {
                $query->withCount('options');
            }
        ])->findOrFail($id);
        
        return view('admin.games.show', compact('game'));
    }

    /**
     * Show the form for editing the specified game
     */
    public function edit($id)
    {
        $game = Game::findOrFail($id);
        return view('admin.games.edit', compact('game'));
    }

    /**
     * Update the specified game
     */
    public function update(Request $request, $id)
    {
        $game = Game::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);
        
        // Upload logo jika ada
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($game->logo) {
                Storage::disk('public')->delete($game->logo);
            }
            
            $logoPath = $request->file('logo')->store('games/logos', 'public');
            $game->logo = $logoPath;
        }
        
        // Upload banner jika ada
        if ($request->hasFile('banner')) {
            // Hapus banner lama jika ada
            if ($game->banner) {
                Storage::disk('public')->delete($game->banner);
            }
            
            $bannerPath = $request->file('banner')->store('games/banners', 'public');
            $game->banner = $bannerPath;
        }
        
        // Update game
        $game->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ]);
        
        return redirect()->route('admin.games.index')
            ->with('success', 'Game berhasil diperbarui!');
    }

    /**
     * Remove the specified game
     */
    public function destroy($id)
    {
        $game = Game::findOrFail($id);
        
        // Hapus file terkait
        if ($game->logo) {
            Storage::disk('public')->delete($game->logo);
        }
        
        if ($game->banner) {
            Storage::disk('public')->delete($game->banner);
        }
        
        // Delete game
        $game->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Game berhasil dihapus!'
        ]);
    }
}