<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Resources\Employee\EmployeeResource;
use App\Models\Employee\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with('users')->get();
        return EmployeeResource::collection($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'user_id' => 'required|unique:employees,user_id',
            'position' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                422
            );
        };

        DB::beginTransaction();
        try {

            Employee::create([
                'name' => $request->name,
                'user_id' => $request->user_id,
                'position' => $request->position
            ]);

            DB::commit();

            return response()->json(['message' => 'Employee created successfully'], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Employee creation failed', 'error' => $th->getMessage()], 500);
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
            'user_id' => 'required',
            'position' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                422
            );
        };

        DB::beginTransaction();
        try {
            $employee = Employee::find($id);
            if (!$employee) {
                return response()->json(['message' => 'Employee not found'], 404);
            }
            $employee->update([
                'name' => $request->name,
                'user_id' => $request->user_id,
                'position' => $request->position
            ]);
            DB::commit();
            return response()->json(['message' => 'Employee updated successfully'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Employee update failed', 'error' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $employee = Employee::find($id);
            if (!$employee) {
                return response()->json(['message' => 'Employee not found'], 404);
            }
            $employee->delete();
            DB::commit();
            return response()->json(['message' => 'Employee deleted successfully'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Employee deletion failed', 'error' => $th->getMessage()], 500);
        }
    }
}
