<?php

namespace App\Models\Access;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Menu extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'route',
        'roles',
        'parent_id',
        'order',
        'icon',
    ];

    protected $casts = [
        'roles' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($menu) {
            $menu->id = (string) Str::uuid();
        });
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }
}
