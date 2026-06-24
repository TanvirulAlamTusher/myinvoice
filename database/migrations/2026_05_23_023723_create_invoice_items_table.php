<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
             $table->id();

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    $table->foreignId('invoice_id')
        ->constrained('invoices')
        ->cascadeOnDelete();

    $table->foreignId('product_id')
        ->nullable()
        ->constrained('products')
        ->nullOnDelete();

    /*
    |--------------------------------------------------------------------------
    | PRODUCT SNAPSHOT (IMPORTANT FOR HISTORY)
    |--------------------------------------------------------------------------
    */

    $table->string('product_name')->nullable();
    $table->string('product_sku')->nullable();

    /*
    |--------------------------------------------------------------------------
    | QUANTITY & UNIT
    |--------------------------------------------------------------------------
    */

    $table->decimal('quantity', 12, 2)->default(0);
    $table->string('unit')->nullable(); // pcs, kg, bag etc

    /*
    |--------------------------------------------------------------------------
    | PRICING
    |--------------------------------------------------------------------------
    */

    // reference purchase price from product (not mandatory but useful for profit calculation)

    $table->decimal('purchase_price', 12, 2)->default(0);
   // actual selling price used in invoice (editable)
    $table->decimal('unit_price', 12, 2)->default(0);

    /*
    |--------------------------------------------------------------------------
    | CALCULATED TOTAL
    |--------------------------------------------------------------------------
    */

    // final row total
    $table->decimal('subtotal', 12, 2)->default(0);



    /*
    |--------------------------------------------------------------------------
    | TIMESTAMPS
    |--------------------------------------------------------------------------
    */

    $table->timestamp('created_at')->useCurrent();
    $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
