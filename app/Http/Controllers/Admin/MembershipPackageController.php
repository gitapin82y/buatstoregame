<?php

// app/Http/Controllers/Admin/MembershipPackageController.php
namespace App\Http\Controllers\Admin;
    
use App\Http\Controllers\Controller;
use App\Models\MembershipPackage;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MembershipPackageController extends Controller
{

    /**
 * Get all packages for select options
 */
public function getPackages()
{
    $packages = MembershipPackage::select('id', 'name', 'level')
        ->where('status', 'active')
        ->get();
        
    return response()->json([
        'success' => true,
        'packages' => $packages
    ]);
}
    /**
     * Display a listing of the membership packages
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $packages = MembershipPackage::withCount(['transactions']);
                
            return DataTables::of($packages)
                ->addColumn('price_formatted', function($package) {
                    return 'Rp ' . number_format($package->price, 0, ',', '.');
                })
                ->addColumn('level_badge', function($package) {
                    if ($package->level === 'gold') {
                        return '<span class="badge bg-warning">Gold</span>';
                    } else {
                        return '<span class="badge bg-secondary">Silver</span>';
                    }
                })
                ->addColumn('status_badge', function($package) {
                    if ($package->status === 'active') {
                        return '<span class="badge bg-success">Active</span>';
                    } else {
                        return '<span class="badge bg-danger">Inactive</span>';
                    }
                })
                ->addColumn('actions', function($package) {
                    return '
                        <a href="' . route('admin.membership-packages.edit', $package->id) . '" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" data-id="' . $package->id . '" class="btn btn-sm btn-danger btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['level_badge', 'status_badge', 'actions'])
                ->make(true);
        }
        
        return view('admin.membership-packages.index');
    }
    
    /**
     * Show the form for creating a new membership package
     */
    public function create()
    {
        return view('admin.membership-packages.create');
    }
    
    /**
     * Store a newly created membership package
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|in:silver,gold',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'status' => 'required|in:active,inactive',
        ]);
        
        // Create package
        $package = MembershipPackage::create([
            'name' => $request->name,
            'level' => $request->level,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'description' => $request->description,
            'features' => $request->features ?? [],
            'status' => $request->status,
        ]);
        
        return redirect()->route('admin.membership-packages.index')
            ->with('success', 'Paket membership berhasil ditambahkan!');
    }
    
    /**
     * Show the form for editing the specified membership package
     */
    public function edit($id)
    {
        $package = MembershipPackage::findOrFail($id);
        return view('admin.membership-packages.edit', compact('package'));
    }
    
    /**
     * Update the specified membership package
     */
    public function update(Request $request, $id)
    {
        $package = MembershipPackage::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|in:silver,gold',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'status' => 'required|in:active,inactive',
        ]);
        
        // Update package
        $package->update([
            'name' => $request->name,
            'level' => $request->level,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'description' => $request->description,
            'features' => $request->features ?? [],
            'status' => $request->status,
        ]);
        
        return redirect()->route('admin.membership-packages.index')
            ->with('success', 'Paket membership berhasil diperbarui!');
    }
    
    /**
     * Remove the specified membership package
     */
    public function destroy($id)
    {
        $package = MembershipPackage::findOrFail($id);
        
        // Cek jika paket sudah digunakan
        if ($package->transactions()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Paket ini tidak dapat dihapus karena sudah digunakan!'
            ]);
        }
        
        // Delete package
        $package->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Paket membership berhasil dihapus!'
        ]);
    }
}
