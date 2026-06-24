<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $fillable = [
        'business_name',
        'owner_name',
        'top_tagline',
        'tagline',
        'phone_1',
        'phone_2',
        'email',
        'website',
        'address',
        'logo',
        'signature',
          'favicon',
        'terms_conditions',
        ];


}
