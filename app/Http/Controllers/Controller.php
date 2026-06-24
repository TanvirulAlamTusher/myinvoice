<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use Illuminate\Support\Facades\View;

abstract class Controller
{
 protected $businessName;

    public function __construct()
    {
       $this->businessName = BusinessSetting::first()?->business_name ?? 'Invoice Maker';
         View::share('businessName', $this->businessName);
    }




}
