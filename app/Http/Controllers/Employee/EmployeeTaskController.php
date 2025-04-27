<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeTask;
use App\Models\Employee\Task;
use App\Services\RemunerationCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeTaskController extends Controller
{
    protected $remunerationCalculator;

    public function __construct(RemunerationCalculatorService $remunerationCalculator)
    {
        $this->remunerationCalculator = $remunerationCalculator;
    }

    public function getId()
    {
        $userId = Auth::user()->id;
        $employeeId = Employee::where('user_id', $userId)->value('id');

        return response()->json([
            'id' => $employeeId
        ]);
    }

    public function listByTask($taskId)
    {
        $employeeTasks = EmployeeTask::with('employee')
            ->where('task_id', $taskId)
            ->get();

        $result = $this->remunerationCalculator->calculateProrated($employeeTasks);

        return response()->json($result);
    }

    public function assignEmployee(Request $request)
    {
        $data = $request->validate([
            'task_id' => 'required|uuid',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'uuid',
        ]);

        foreach ($data['employee_ids'] as $employeeId) {
            EmployeeTask::firstOrCreate([
                'task_id' => $data['task_id'],
                'employee_id' => $employeeId
            ]);
        }

        return response()->json(['message' => 'Employees assigned successfully']);
    }

    public function removeEmployee($id)
    {
        EmployeeTask::findOrFail($id)->delete();
        return response()->json(['message' => 'Employee unassigned successfully']);
    }

    public function myTasks()
    {
        $userId = Auth::id();
        $employeeId = Employee::where('user_id', $userId)->value('id');

        $tasks = Task::whereHas('employees', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })
            ->with('employees')
            ->get();

        return response()->json($tasks);
    }

    public function updateMyTask(Request $request, $id)
    {
        $userId = Auth::user()->id;
        $employeeId = Employee::where('user_id', $userId)->value('id');

        $task = EmployeeTask::where('employee_id', $employeeId)->where('id', $id)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'hours_spent' => 'required|numeric',
            'hourly_rate' => 'required|numeric',
            'additional_charges' => 'nullable|numeric',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $task->update([
                'hours_spent' => $request->hours_spent,
                'hourly_rate' => $request->hourly_rate,
                'additional_charges' => $request->additional_charges,
                'note' => $request->note,
            ]);
            DB::commit();
            return response()->json(['message' => 'Task updated successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Task update failed', 'error' => $th->getMessage()], 500);
        }
    }
}
