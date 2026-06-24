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
        Schema::create('invoices', function (Blueprint $table) {

         $table->id();

    /*
    |--------------------------------------------------------------------------
    | USER FOOTPRINT
    |--------------------------------------------------------------------------
    */

    $table->foreignId('created_by')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

    $table->foreignId('updated_by')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

    /*
    |--------------------------------------------------------------------------
    | INVOICE INFO
    |--------------------------------------------------------------------------
    */

    $table->string('invoice_no')->unique();
    $table->dateTime('invoice_date');

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER RELATION + SNAPSHOT
    |--------------------------------------------------------------------------
    */

    $table->foreignId('customer_id')
        ->nullable()
        ->constrained('customers')
        ->nullOnDelete();

    // snapshot (important for history safety)
    $table->string('customer_name')->nullable();
    $table->string('customer_business_name')->nullable();
    $table->string('customer_phone')->nullable();
    $table->string('customer_email')->nullable();
    $table->text('customer_address')->nullable();

    /*
    |--------------------------------------------------------------------------
    | AMOUNT SUMMARY
    |--------------------------------------------------------------------------
    */

    $table->decimal('sub_total', 12, 2)->default(0);
    $table->decimal('discount_amount', 12, 2)->default(0);
    $table->decimal('tax_amount', 12, 2)->default(0);
    $table->decimal('grand_total', 12, 2)->default(0);
     $table->decimal('paid_amount', 12, 2)->default(0);

    /*
    |--------------------------------------------------------------------------
    | PAYMENT TRACKING
    |--------------------------------------------------------------------------
    */

    $table->enum('payment_status', [
        'paid',
        'partial',
        'due'
    ])->default('due');

    $table->enum('payment_method', [
        'cash',
        'bank',
        'cheque',
        'mobile_banking',
        'credit'
    ])->default('cash');

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    $table->enum('status', [
        'draft',
        'completed',
        'cancelled'
    ])->default('completed');

    /*
    |--------------------------------------------------------------------------
    | EXTRA INFO
    |--------------------------------------------------------------------------
    */

    $table->text('note')->nullable();

    /*
    |--------------------------------------------------------------------------
    | TIMESTAMPS
    |--------------------------------------------------------------------------
    */

    $table->timestamp('created_at')->useCurrent();
    $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

    /*
    |--------------------------------------------------------------------------
    | SOFT DELETE
    |--------------------------------------------------------------------------
    */

    $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
