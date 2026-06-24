<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
 use SoftDeletes;

    protected $fillable = [
        'created_by',
        'updated_by',
        'invoice_no',
        'invoice_date',

        'customer_id',
        'customer_name',
        'customer_business_name',
        'customer_phone',
        'customer_email',
        'customer_address',

        'sub_total',
        'discount_amount',
        'tax_amount',
        'grand_total',

        'paid_amount',
        'payment_status',
        'payment_method',

        'status',
        'note',
    ];

 protected $casts = [
    'invoice_date' => 'datetime',
];
    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Creator user
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Updater user
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Invoice Items
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
    public function productReturns()
{
    return $this->hasMany(ProductReturn::class);
}



}
