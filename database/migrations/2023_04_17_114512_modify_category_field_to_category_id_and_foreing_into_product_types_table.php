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
        if (!Schema::hasColumn('product_types', 'category_id')) {
            Schema::table('product_types', function (Blueprint $table) {
                $table->dropColumn('category');
                $table->foreignId('category_id')->after('id')->nullable()->constrained('categories')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_types', function (Blueprint $table) {
            $table->enum(
                'category',
                ['General', 'Promociones', 'Masajes-Tratamientos', 'Belleza-Salud', 'Tratamientos-Premium', 'Circuitos']
            )->index();
            $table->removeColumn('category_id');
        });
    }
};
