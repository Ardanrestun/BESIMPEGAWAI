<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Resources\Employee\TaskResource;
use App\Models\Employee\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::all();
        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'nullable',
            'due_date' => 'required|date',
            'deadline_date' => 'required|date|after_or_equal:due_date',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                422
            );
        }

        DB::beginTransaction();
        try {
            Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'deadline_date' => $request->deadline_date,
                'is_completed' => (bool) $request->status,
            ]);

            DB::commit();
            return response()->json(['message' => 'Task created successfully'], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Task creation failed', 'error' => $th->getMessage()], 500);
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
            'title' => 'nullable',
            'description' => 'nullable',
            'due_date' => 'nullable|date',
            'deadline_date' => 'nullable|date|after_or_equal:due_date',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                422
            );
        }

        DB::beginTransaction();
        try {
            $task = Task::find($id);
            if (!$task) {
                return response()->json(['message' => 'Task not found'], 404);
            }
            $task->update([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'deadline_date' => $request->deadline_date,
                'is_completed' => (bool) $request->status,
            ]);

            DB::commit();
            return response()->json(['message' => 'Task updated successfully'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Task update failed', 'error' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $task = Task::find($id);
            if (!$task) {
                return response()->json(['message' => 'Task not found'], 404);
            }
            $task->delete();
            DB::commit();
            return response()->json(['message' => 'Task deleted successfully'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Task deletion failed', 'error' => $th->getMessage()], 500);
        }
    }
}
