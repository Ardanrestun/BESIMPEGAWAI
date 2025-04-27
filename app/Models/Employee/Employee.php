<?php

namespace App\Models\Employee;

use App\Models\Access\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Employee extends Model
{

    protected $table = 'employees';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'position',
        'user_id',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }


    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'employee_task')
            ->withPivot('hours_spent', 'hourly_rate', 'additional_charges', 'total_remuneration')
            ->withTimestamps();
    }
}
