<?php
// app/Http/Controllers/Reseller/ContentController.php
namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Game;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ContentController extends Controller
{
    /**
     * Display content list
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        // Check if Gold membership required
        if ($reseller->membership_level !== 'gold' || !$reseller->isActive()) {
            return redirect()->route('reseller.dashboard')
                ->with('error', 'Fitur ini hanya tersedia untuk paket Gold yang aktif.');
        }
        
        if ($request->ajax()) {
            $contents = Content::where('reseller_id', $reseller->id);
                
            return DataTables::of($contents)
                ->addColumn('type_badge', function($content) {
                    if ($content->type === 'post') {
                        return '<span class="badge bg-primary">Post</span>';
                    } elseif ($content->type === 'caption') {
                        return '<span class="badge bg-info">Caption</span>';
                    } else {
                        return '<span class="badge bg-success">Image</span>';
                    }
                })
                ->addColumn('platform_badge', function($content) {
                    if ($content->platform === 'instagram') {
                        return '<span class="badge bg-purple">Instagram</span>';
                    } elseif ($content->platform === 'facebook') {
                        return '<span class="badge bg-primary">Facebook</span>';
                    } elseif ($content->platform === 'twitter') {
                        return '<span class="badge bg-info">Twitter</span>';
                    } else {
                        return '<span class="badge bg-secondary">All</span>';
                    }
                })
                ->addColumn('status_badge', function($content) {
                    if ($content->status === 'published') {
                        return '<span class="badge bg-success">Published</span>';
                    } elseif ($content->status === 'scheduled') {
                        return '<span class="badge bg-warning">Scheduled</span>';
                    } else {
                        return '<span class="badge bg-secondary">Draft</span>';
                    }
                })
                ->addColumn('date', function($content) {
                    return $content->created_at->format('d M Y H:i');
                })
                ->addColumn('actions', function($content) {
                    return '
                        <a href="' . route('reseller.content.edit', $content->id) . '" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" data-id="' . $content->id . '" class="btn btn-sm btn-info btn-view">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" data-id="' . $content->id . '" class="btn btn-sm btn-danger btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['type_badge', 'platform_badge', 'status_badge', 'actions'])
                ->make(true);
        }
        
        // Get games for selection
        $games = Game::whereHas('resellerGames', function($query) use ($reseller) {
            $query->where('reseller_id', $reseller->id)
                ->where('is_active', true);
        })->get();
        
        return view('reseller.content.index', compact('games'));
    }
    
    /**
     * Generate AI content
     */
    public function generate(Request $request, AiService $aiService)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        // Check if Gold membership required
        if ($reseller->membership_level !== 'gold' || !$reseller->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur ini hanya tersedia untuk paket Gold yang aktif.'
            ]);
        }
        
        $request->validate([
            'prompt' => 'required|string|max:500',
            'platform' => 'required|in:instagram,facebook,twitter,all',
            'type' => 'required|in:caption,post,image',
            'game_id' => 'nullable|exists:games,id',
        ]);
        
        try {
            // Get game name if provided
            $gameName = '';
            if ($request->filled('game_id')) {
                $game = Game::find($request->game_id);
                if ($game) {
                    $gameName = $game->name;
                }
            }
            
            // Generate content based on type
            if ($request->type === 'image') {
                // For images, we'll generate both image and caption
                $prompt = $request->prompt;
                if ($gameName) {
                    $prompt .= " for the game {$gameName}";
                }
                
                $imageUrl = $aiService->generateImage($prompt);
                $caption = $aiService->generateContent($prompt, $request->platform, 'caption');
                
                return response()->json([
                    'success' => true,
                    'type' => 'image',
                    'image_url' => $imageUrl,
                    'content' => $caption,
                    'message' => 'Image generated successfully!'
                ]);
            } else {
                // For caption or post
                $prompt = $request->prompt;
                if ($gameName) {
                    $prompt .= " for the game {$gameName}";
                }
                
                $content = $aiService->generateContent($prompt, $request->platform, $request->type);
                
                return response()->json([
                    'success' => true,
                    'type' => $request->type,
                    'content' => $content,
                    'message' => 'Content generated successfully!'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating content: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Generate content calendar
     */
    public function generateCalendar(Request $request, AiService $aiService)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        // Check if Gold membership required
        if ($reseller->membership_level !== 'gold' || !$reseller->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur ini hanya tersedia untuk paket Gold yang aktif.'
            ]);
        }
        
        $request->validate([
            'game_id' => 'required|exists:games,id',
        ]);
        
        try {
            $game = Game::find($request->game_id);
            
            $calendar = $aiService->generateContentCalendar($reseller, $game->name);
            
            return response()->json([
                'success' => true,
                'calendar' => $calendar,
                'message' => 'Content calendar generated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating calendar: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Store content
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        // Check if Gold membership required
        if ($reseller->membership_level !== 'gold' || !$reseller->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur ini hanya tersedia untuk paket Gold yang aktif.'
            ]);
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|string',
            'type' => 'required|in:post,caption,image',
            'platform' => 'required|string',
            'status' => 'required|in:draft,published,scheduled',
            'scheduled_at' => 'nullable|required_if:status,scheduled|date',
        ]);
        
        // Save image from URL if provided
        $imagePath = null;
        if ($request->filled('image') && filter_var($request->image, FILTER_VALIDATE_URL)) {
            $imageContent = file_get_contents($request->image);
            $filename = 'content/' . time() . '.png';
            
            // Save to storage
            Storage::disk('public')->put($filename, $imageContent);
            $imagePath = $filename;
        }
        
        // Create content
        $content = Content::create([
            'reseller_id' => $reseller->id,
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imagePath,
            'type' => $request->type,
            'platform' => $request->platform,
            'status' => $request->status,
            'scheduled_at' => $request->status === 'scheduled' ? $request->scheduled_at : null,
        ]);
        
        return response()->json([
            'success' => true,
            'content' => $content,
            'message' => 'Content saved successfully!'
        ]);
    }
    
    /**
     * Get content details
     */
    public function show($id)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return response()->json([
                'success' => false,
                'message' => 'Reseller profile not found'
            ]);
        }
        
        $content = Content::where('reseller_id', $reseller->id)
            ->findOrFail($id);
            
        return response()->json([
            'success' => true,
            'content' => $content,
            'image_url' => $content->image ? asset('storage/' . $content->image) : null,
        ]);
    }
    
    /**
     * Edit content form
     */
    public function edit($id)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        $content = Content::where('reseller_id', $reseller->id)
            ->findOrFail($id);
            
        return view('reseller.content.edit', compact('content'));
    }
    
    /**
     * Update content
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return redirect()->route('reseller.setup');
        }
        
        $content = Content::where('reseller_id', $reseller->id)
            ->findOrFail($id);
            
        $request->validate([
            'title' => 'required|string|max:255',
            'content_text' => 'required|string',
            'type' => 'required|in:post,caption,image',
            'platform' => 'required|string',
            'status' => 'required|in:draft,published,scheduled',
            'scheduled_at' => 'nullable|required_if:status,scheduled|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Upload image if provided
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($content->image) {
                Storage::disk('public')->delete($content->image);
            }
            
            $imagePath = $request->file('image')->store('content', 'public');
            $content->image = $imagePath;
        }
        
        // Update content
        $content->update([
            'title' => $request->title,
            'content' => $request->content_text,
            'type' => $request->type,
            'platform' => $request->platform,
            'status' => $request->status,
            'scheduled_at' => $request->status === 'scheduled' ? $request->scheduled_at : null,
        ]);
        
        return redirect()->route('reseller.content.index')
            ->with('success', 'Content berhasil diperbarui!');
    }
    
    /**
     * Delete content
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $reseller = $user->resellerProfile;
        
        if (!$reseller) {
            return response()->json([
                'success' => false,
                'message' => 'Reseller profile not found'
            ]);
        }
        
        $content = Content::where('reseller_id', $reseller->id)
            ->findOrFail($id);
            
        // Delete image if exists
        if ($content->image) {
            Storage::disk('public')->delete($content->image);
        }
        
        // Delete content
        $content->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Content berhasil dihapus!'
        ]);
    }
}