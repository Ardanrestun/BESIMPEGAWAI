<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\Controller;
use App\Http\Resources\Access\MenuResource;
use App\Models\Access\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function menuRole(Request $request)
    {
        $roleName = $request->user()->role->name;

        $menus = Menu::whereJsonContains('roles', $roleName)
            ->whereNull('parent_id')
            ->with(['children' => function ($q) use ($roleName) {
                $q->whereJsonContains('roles', $roleName);
            }])
            ->orderBy('order')
            ->get();

        return response()->json($menus);
    }

    public function index()
    {

        $menus = Menu::with('children')->whereNull('parent_id')->orderBy('order', 'asc')->get();
        return MenuResource::collection($menus);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'icon' => 'nullable|string',
            'route' => 'nullable|string',
            'roles' => 'required|array',
            'order' => 'required|integer',
            'parent_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $menu = Menu::create([
                'name' => $request->name,
                'icon' => $request->icon,
                'route' => $request->url,
                'roles' => $request->roles,
                'order' => $request->order,
                'parent_id' => $request->parent_id,
            ]);
            DB::commit();

            return response()->json($menu, 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Menu creation failed.', 'error' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $menu = Menu::findOrFail($id);
            $menu->update([
                'name' => $request->name,
                'icon' => $request->icon,
                'route' => $request->url,
                'roles' => $request->roles,
                'order' => $request->order,
                'parent_id' => $request->parent_id,
            ]);
            DB::commit();

            return response()->json($menu, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Menu update failed.', 'error' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $menu = Menu::findOrFail($id);
            $menu->delete();
            DB::commit();
            return response()->json(['message' => 'Menu deleted successfully'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Menu deletion failed.', 'error' => $th->getMessage()], 500);
        }
    }
}
