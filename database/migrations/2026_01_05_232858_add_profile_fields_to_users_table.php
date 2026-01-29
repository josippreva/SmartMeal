<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // AI personalizacija
            $table->integer('daily_calorie_target')->nullable()->after('preferences');
            $table->string('diet_type')->nullable()->after('daily_calorie_target'); // vegan, vegetarian, keto, etc.
            $table->json('allergies')->nullable()->after('diet_type');
            $table->json('available_ingredients')->nullable()->after('allergies');
            
            // Fizički podaci
            $table->integer('age')->nullable()->after('available_ingredients');
            $table->string('gender')->nullable()->after('age');
            $table->decimal('weight', 5, 2)->nullable()->after('gender'); // kg
            $table->decimal('height', 5, 2)->nullable()->after('weight'); // cm
            $table->string('activity_level')->nullable()->after('height'); // sedentary, light, moderate, active, very_active
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'daily_calorie_target',
                'diet_type',
                'allergies',
                'available_ingredients',
                'age',
                'gender',
                'weight',
                'height',
                'activity_level'
            ]);
        });
    }
};