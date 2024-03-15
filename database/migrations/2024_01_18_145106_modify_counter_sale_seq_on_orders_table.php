<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('counter_sale_seq')->unique()->nullable()->change();
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_counter_sale_seq_unique');
            $table->string('counter_sale_seq')->nullable()->change();
        });
    }
};
