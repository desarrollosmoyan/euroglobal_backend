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
        Schema::table('gym_subscription_payments', function (Blueprint $table) {
            $table->foreignId('order_id')
                ->after('gym_subscription_id')
                ->constrained()
                ->restrictOnDelete()
                ->restrictOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gym_subscription_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('order_id');
        });
    }
};
