<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Task extends Model
{

    protected $table = 'tasks';


    protected $keyType = 'string';
    public $incrementing = false;


    protected $fillable = [
        'title',
        'description',
        'due_date',
        'deadline_date',
        'is_completed'
    ];



    protected $casts = [
        'is_completed' => 'boolean',
        'due_date' => 'date',
        'deadline_date' => 'date',
    ];



    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }


    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_task')
            ->withPivot('hours_spent', 'hourly_rate', 'additional_charges', 'total_remuneration')
            ->withTimestamps();
    }
}
