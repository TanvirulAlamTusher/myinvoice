<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReturnItem extends Model
{
        protected $fillable = [
        'product_return_id',
        'invoice_item_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'subtotal',
    ];
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function productReturn()
    {
        return $this->belongsTo(ProductReturn::class);
    }

    public function invoiceItem()
    {
        return $this->belongsTo(InvoiceItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
