<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceTemplate extends Model
{
       protected $fillable = [

        'name',
        'view_name',
        'preview_image',
        'is_default',
        'status',
    ];
}
