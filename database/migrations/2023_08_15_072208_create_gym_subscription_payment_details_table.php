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
        Schema::create('gym_subscription_payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_subscription_payment_id')->constrained()->restrictOnDelete()->name('gym_subscription_payment_id_foreign');
            $table->string('type');
            $table->foreignId('gym_subscription_member_id')->nullable()->constrained()->restrictOnDelete()->name('gym_subscription_member_id_foreign');
            $table->decimal('price');
            $table->tinyInteger('quantity');
            $table->decimal('amount');
            $table->foreignId('created_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('last_modified_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->foreign('gym_subscription_payment_id', 'gym_subscription_payments_id_foreign')->references('id')->on('gym_subscription_payments')->restrictOnDelete();
            $table->foreign('gym_subscription_member_id', 'gym_subscription_members_id_foreign')->references('id')->on('gym_subscription_members')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_subscription_payment_details');
    }
};
