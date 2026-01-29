<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->float('calories_per_100g')->nullable()->after('name');
            $table->float('protein_per_100g')->nullable()->after('calories_per_100g');
            $table->float('carbs_per_100g')->nullable()->after('protein_per_100g');
            $table->float('fat_per_100g')->nullable()->after('carbs_per_100g');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn(['calories_per_100g', 'protein_per_100g', 'carbs_per_100g', 'fat_per_100g']);
        });
    }
};
