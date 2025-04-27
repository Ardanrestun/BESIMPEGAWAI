<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmployeeTask extends Model
{
    protected $table = 'employee_task';

    protected $fillable = [
        'employee_id',
        'task_id',
        'hours_spent',
        'hourly_rate',
        'additional_charges',
        'total_remuneration',
        'note'
    ];

    protected $keyType = 'string';
    public $incrementing = false;


    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }


    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
