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
            $table->unsignedBigInteger('salt_sessions')->default(0)->after('treatment_sessions');
        });

        DB::table('products')
            ->where('product_type_id', 301)
            ->update([
                'salt_sessions' => DB::raw('treatment_sessions'),
                'treatment_sessions' => 0,
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
            $table->dropColumn('salt_sessions');
        });
    }
};
