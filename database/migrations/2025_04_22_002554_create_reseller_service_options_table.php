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
        Schema::create('reseller_service_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_game_service_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_option_id')->references('id')->on('service_options')->onDelete('cascade');
            $table->decimal('selling_price', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['reseller_game_service_id', 'service_option_id'], 'rso_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseller_service_options');
    }
};
