<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\Controller;
use App\Http\Resources\Access\UserResource;
use App\Jobs\Access\ProcessNewUser;
use App\Models\Access\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index()
    {
        $user = User::with('role')->get();
        return UserResource::collection($user);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role_id' => $request->role_id,
            ]);

            ProcessNewUser::dispatch($user);
            DB::commit();
            return response()->json(['message' => 'User created successfully'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to create user',
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
            'name' => 'nullable',
            'email' => 'nullable|email',
            'password' => 'nullable|min:8',
            'role_id' => 'nullable',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        DB::beginTransaction();
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role_id' => $request->role_id,
            ]);
            DB::commit();
            return response()->json(['message' => 'User updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to update user',
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
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $user->delete();
            DB::commit();
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to delete user',
                'message' => $th->getMessage()
            ], 500);
        }
    }



    public function getEmployees()
    {
        try {
            $user = User::with('role')->whereHas('role', function ($query) {
                $query->where('name', 'Employee');
            })->get();
            return UserResource::collection($user);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Failed to get employees',
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
