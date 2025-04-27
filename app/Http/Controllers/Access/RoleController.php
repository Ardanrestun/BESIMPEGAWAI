<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\Controller;
use App\Http\Resources\Access\RoleResource;
use App\Jobs\Access\ProcessNewRole;
use App\Models\Access\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $roles = Role::latest()->get();
            return RoleResource::collection($roles);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Role not found'], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            Log::info('Creating a new role: ' . $request->name);
            $role = Role::create([
                'name' => $request->name,
            ]);
            Log::info('Role created successfully: ' . $role->id);
            ProcessNewRole::dispatch($role->id);
            DB::commit();
            Log::info('Role created and dispatched to job');
            return response()->json(['message' => 'Role created successfully'], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create role: ' . $e->getMessage());
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to create role',
                'message' => $e->getMessage()
            ], 500);
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();


        try {
            $role = Role::find($id);

            if (!$role) {
                return response()->json(['message' => 'Role not found'], 404);
            }

            $role->update([
                'name' => $request->name,
            ]);


            DB::commit();

            return response()->json(['message' => 'Role updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Failed to update role',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        DB::beginTransaction();
        try {
            $role = Role::find($id);
            if (!$role) {
                return response()->json(['message' => 'Role not found'], 404);
            }
            $role->delete();
            DB::commit();
            return response()->json(['message' => 'Role deleted successfully'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to delete role',
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
