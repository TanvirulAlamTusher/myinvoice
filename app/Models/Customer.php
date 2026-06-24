<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'business_name',
        'phone',
        'alternative_phone',
        'email',
        'address',
        'is_active',
    ];


    public function invoices()
{
    return $this->hasMany(Invoice::class);
}
}
