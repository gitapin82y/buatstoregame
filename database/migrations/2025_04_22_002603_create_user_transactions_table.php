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
        Schema::create('user_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reseller_id')->references('id')->on('reseller_profiles')->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->references('id')->on('game_services')->onDelete('cascade');
            $table->foreignId('option_id')->references('id')->on('service_options')->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->string('user_identifier')->nullable(); // Misalnya User ID game
            $table->string('server_identifier')->nullable(); // Misalnya Server ID game
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired'])->default('pending');
            $table->enum('process_status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('xendit_invoice_id')->nullable();
            $table->string('payment_link')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_transactions');
    }
};
