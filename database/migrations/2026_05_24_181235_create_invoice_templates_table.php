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
        Schema::create('invoice_templates', function (Blueprint $table) {
                $table->id();

            /*
            |--------------------------------------------------------------------------
            | Template Info
            |--------------------------------------------------------------------------
            */

            $table->string('name');
            // Example:
            // Classic A4
            // Modern A4
            // Thermal POS

            $table->string('view_name')->unique();
            // classic
            // modern
            // thermal

            /*
            |--------------------------------------------------------------------------
            | Design
            |--------------------------------------------------------------------------
            */

            $table->string('preview_image')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Settings
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_default')
                ->default(false);

            $table->boolean('status')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamp('created_at')
                ->useCurrent();

            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_templates');
    }
};
