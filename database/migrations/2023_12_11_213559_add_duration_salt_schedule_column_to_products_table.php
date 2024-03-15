<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('products', function (Blueprint $table) {
            $table->smallInteger('duration_salt_schedule')->unsigned()->nullable()->after('duration_circuit_schedule');
        });

        DB::table('products')
            ->where('product_type_id', 301)
            ->update([
                'duration_salt_schedule' => DB::raw('duration_treatment_schedule'),
                'duration_treatment_schedule' => 0,
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('duration_salt_schedule');
        });
    }
};
