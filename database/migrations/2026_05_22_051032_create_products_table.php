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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // BASIC INFO
            $table->string('name');
            $table->string('sku')->unique()->nullable(); // optional internal code
            $table->string('barcode')->unique()->nullable();

            // RELATIONS (ALL OPTIONAL)
            $table->foreignId('category_id')->nullable()->constrained('categories')->restrictOnDelete();

            $table->foreignId('brand_id')->nullable()->constrained('brands')->restrictOnDelete();

            $table->foreignId('product_unit_id')->nullable()->constrained('product_units')->nullOnDelete();

            // PRICING
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2);

            // STOCK MANAGEMENT
            $table->integer('stock')->default(0);
            $table->integer('alert_stock')->default(5); // low stock warning
            //weight and dimensions for shipping calculations
            $table->decimal('weight', 8, 2)->nullable(); // in kg

            // OPTIONAL INFO
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            // STATUS
            $table->boolean('is_active')->default(true);

            // TIMESTAMP
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
