<?php

namespace App\Services;

use App\Models\Employee\EmployeeTask;
use Illuminate\Support\Collection;

class RemunerationCalculatorService
{
    /**
     *
     * @param Collection $employeeTasks
     * @return array
     */
    public function calculateProrated(Collection $employeeTasks): array
    {
        $totalHours = $employeeTasks->sum('hours_spent');

        $totalCost = $employeeTasks->reduce(function ($carry, $item) {
            return $carry + ($item->hours_spent * $item->hourly_rate) + ($item->additional_charges ?? 0);
        }, 0);

        // Hindari division by zero
        if ($totalHours <= 0) {
            $totalHours = 1;
        }

        $employeeTasks = $employeeTasks->map(function ($item) use ($totalHours, $totalCost) {
            $contribution = $item->hours_spent / $totalHours;
            $item->prorated_remuneration = $contribution * $totalCost;
            return $item;
        });

        return [
            'task_total_remuneration' => $totalCost,
            'employees' => $employeeTasks
        ];
    }
}
