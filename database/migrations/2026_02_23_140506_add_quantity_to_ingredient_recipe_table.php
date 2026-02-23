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
        Schema::table('ingredient_recipe', function (Blueprint $table) {
            $table->decimal('quantity', 8, 2)->default(100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredient_recipe', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
