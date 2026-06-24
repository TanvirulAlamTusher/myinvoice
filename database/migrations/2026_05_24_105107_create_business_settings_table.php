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
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();



            // Company
            $table->string('business_name')->nullable();
            $table->string('owner_name')->nullable();

            // Header
            $table->string('top_tagline')->nullable();
            $table->string('tagline')->nullable();

            // Contact
            $table->string('phone_1')->nullable();
            $table->string('phone_2')->nullable();

            $table->string('email')->nullable();
            $table->string('website')->nullable();

            $table->text('address')->nullable();

            // Files
            $table->string('logo')->nullable();
            $table->string('signature')->nullable();
            $table->string('favicon')->nullable();

            // Footer
            $table->text('terms_conditions')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_settings');
    }
};
