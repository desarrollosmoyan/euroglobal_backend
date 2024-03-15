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
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'work_schedule_mondays',
                'work_schedule_tuesdays',
                'work_schedule_wednesdays',
                'work_schedule_thursdays',
                'work_schedule_fridays',
                'work_schedule_saturdays',
                'work_schedule_sundays'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->after('notes', function () use ($table) {
                $table->json('work_schedule_mondays')->nullable();
                $table->json('work_schedule_tuesdays')->nullable();
                $table->json('work_schedule_wednesdays')->nullable();
                $table->json('work_schedule_thursdays')->nullable();
                $table->json('work_schedule_fridays')->nullable();
                $table->json('work_schedule_saturdays')->nullable();
                $table->json('work_schedule_sundays')->nullable();
            });
        });
    }
};
