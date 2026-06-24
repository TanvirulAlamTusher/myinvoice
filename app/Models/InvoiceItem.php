<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
       protected $fillable = [
        'invoice_id',
        'product_id',

        'product_name',
        'product_sku',

        'quantity',
        'unit',


        'purchase_price',
        'unit_price',

        'subtotal',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Invoice parent
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Product relation
    public function product()
    {
        return $this->belongsTo(Product::class);
    }



    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS (OPTIONAL BUT USEFUL)
    |--------------------------------------------------------------------------
    */

    // Calculate subtotal dynamically (if needed)
    public function calculateSubtotal()
    {
        return ($this->quantity * $this->unit_price);
    }

public function returnItems()
{
    return $this->hasMany(ProductReturnItem::class);
}

public function getReturnedQuantityAttribute()
{
    return $this->returnItems()->sum('quantity');
}

public function getAvailableReturnQuantityAttribute()
{
    return $this->quantity - $this->returned_quantity;
}
}
