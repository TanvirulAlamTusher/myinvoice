<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'image',
        'description',
        'is_active',
    ];

     protected $casts = [
        'is_active' => 'boolean',
    ];
}
