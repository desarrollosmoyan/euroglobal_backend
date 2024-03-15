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
    public function up()
    {
        Schema::create('gym_subscription_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->foreignId('gym_fee_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('gym_fee_type_name');
            $table->unsignedInteger('version')->default(1);
            $table->unsignedInteger('duration_number_of_days');
            $table->decimal('price');
            $table->decimal('price_beneficiaries')->nullable();
            $table->date('activation_date');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('expiration_date');
            $table->integer('payment_day');
            $table->integer('biweekly_payment_day')->nullable();
            $table->string('payment_type');
            $table->foreignId('created_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('last_modified_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('gym_subscription_id')->constrained('gym_subscriptions')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gym_subscription_versions');
    }
};
