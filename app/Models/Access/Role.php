<?php

namespace App\Models\Access;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{

    protected $table = 'access.roles';

    protected $keyType = 'string';
    public $incrementing = false;


    protected $fillable = [
        'name',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }


    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class);
    }
}
