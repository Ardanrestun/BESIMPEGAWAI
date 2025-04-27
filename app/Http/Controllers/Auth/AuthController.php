<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Access\Menu;
use App\Models\Access\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $user = User::with('role')->where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials.'], 401);
            }

            $token = $user->createToken('api-token')->plainTextToken;
            $roleName = $user->role->name;

            $allMenus = Menu::whereJsonContains('roles', $roleName)
                ->orderBy('order')
                ->get();

            $menus = $allMenus->whereNull('parent_id')->values()->map(function ($parent) use ($allMenus, $roleName) {
                $children = $allMenus->where('parent_id', $parent->id)->filter(function ($child) use ($roleName) {
                    return in_array($roleName, $child->roles);
                })->values()->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'route' => $child->route,
                        'roles' => $child->roles,
                        'parent_id' => $child->parent_id,
                        'order' => $child->order,
                        'icon' => $child->icon,
                        'created_at' => $child->created_at,
                        'updated_at' => $child->updated_at,
                    ];
                });

                return [
                    'id' => $parent->id,
                    'name' => $parent->name,
                    'route' => $parent->route,
                    'roles' => $parent->roles,
                    'parent_id' => $parent->parent_id,
                    'order' => $parent->order,
                    'icon' => $parent->icon,
                    'created_at' => $parent->created_at,
                    'updated_at' => $parent->updated_at,
                    'children' => $children,
                ];
            });

            DB::commit();

            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $roleName,
                    'menus' => $menus,
                ]
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Login failed.',
                'error' => $th->getMessage()
            ], 500);
        }
    }


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


    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out.']);
    }
}
