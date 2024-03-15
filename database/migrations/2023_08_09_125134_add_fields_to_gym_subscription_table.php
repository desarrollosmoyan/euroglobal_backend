<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('gym_subscriptions', function (Blueprint $table) {
            $table->decimal('price_beneficiaries')->nullable()->after('price');
            $table->unsignedInteger('version')->default(1)->after('gym_fee_type_name');
            $table->unsignedInteger('duration_number_of_days')->after('version');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('gym_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['price_beneficiaries', 'version', 'duration_number_of_days']);
        });
    }
};
