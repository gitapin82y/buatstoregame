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
        Schema::create('reseller_game_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_game_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_service_id')->references('id')->on('game_services')->onDelete('cascade');
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('profit_margin', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->unique(['reseller_game_id', 'game_service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseller_game_services');
    }
};
