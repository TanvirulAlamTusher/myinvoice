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
        Schema::create('product_return_items', function (Blueprint $table) {
              $table->id();

            /*
            |--------------------------------------------------------------------------
            | RELATIONS
            |--------------------------------------------------------------------------
            */

            $table->foreignId('product_return_id')
                ->constrained('product_returns')
                ->cascadeOnDelete();

            $table->foreignId('invoice_item_id')
                ->constrained('invoice_items')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | PRODUCT SNAPSHOT
            |--------------------------------------------------------------------------
            */

            $table->string('product_name')->nullable();

            /*
            |--------------------------------------------------------------------------
            | RETURN DETAILS
            |--------------------------------------------------------------------------
            */

            $table->decimal('quantity', 12, 2)
                ->default(0);

            $table->decimal('unit_price', 12, 2)
                ->default(0);

            $table->decimal('subtotal', 12, 2)
                ->default(0);


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
        Schema::dropIfExists('product_return_items');
    }
};
