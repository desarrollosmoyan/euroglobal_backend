<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('gym_subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_subscription_id')->constrained()->restrictOnDelete();
            $table->date('previous_expiration_date')->nullable();
            $table->date('next_expiration_date');
            $table->decimal('amount');
            $table->date('date');
            $table->foreignId('created_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('last_modified_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_subscription_payments');
    }
};
