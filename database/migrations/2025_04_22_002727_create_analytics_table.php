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
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->references('id')->on('reseller_profiles')->onDelete('cascade');
            $table->date('date');
            $table->decimal('total_sales', 12, 2)->default(0);
            $table->integer('total_transactions')->default(0);
            $table->decimal('total_profit', 12, 2)->default(0);
            $table->string('popular_game')->nullable();
            $table->string('popular_service')->nullable();
            $table->timestamps();
            
            $table->unique(['reseller_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};
