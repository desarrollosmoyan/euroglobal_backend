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
        Schema::create('salt_room_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->date('date')->nullable();
            $table->char('time', '5')->nullable();
            $table->unsignedTinyInteger('duration')->nullable();
            $table->unsignedInteger('adults');
            $table->unsignedInteger('children');
            $table->boolean('used')->default(false);
            $table->longText('notes')->nullable();
            $table->string('schedule_note')->nullable();
            $table->unsignedInteger('treatment_reservations')->nullable();
            $table->string('notification_email')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('last_modified_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salt_room_reservations');
    }
};
