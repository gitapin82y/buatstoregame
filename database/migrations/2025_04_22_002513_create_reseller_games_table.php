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
        Schema::create('reseller_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->references('id')->on('reseller_profiles')->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->decimal('profit_margin', 5, 2)->default(0);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->unique(['reseller_id', 'game_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseller_games');
    }
};
