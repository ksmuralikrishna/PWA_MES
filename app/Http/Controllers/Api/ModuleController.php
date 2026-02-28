<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * GET /api/modules
     */
    public function index()
    {
        $modules = Module::orderBy('sort_order')->get();
        return response()->json(['status' => 'ok', 'data' => $modules]);
    }

    /**
     * POST /api/modules
     * Admin only
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'slug'        => 'required|string|max:100|unique:modules,slug|alpha_dash',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $module = Module::create($request->only(['name', 'slug', 'description', 'sort_order', 'is_active']));

        return response()->json(['status' => 'ok', 'data' => $module], 201);
    }

    /**
     * PUT /api/modules/{id}
     * Admin only
     */
    public function update(Request $request, string $id)
    {
        $module = Module::findOrFail($id);

        $request->validate([
            'name'        => 'sometimes|required|string|max:100',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $module->update($request->only(['name', 'description', 'sort_order', 'is_active']));

        return response()->json(['status' => 'ok', 'data' => $module]);
    }
}
