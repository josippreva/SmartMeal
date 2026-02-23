<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'name',
        'instructions',  
        'calories',
        'protein',
        'carbs',
        'fat',
        'prep_time',
        'user_id',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'ingredient_recipe')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function calculateNutrients(): array
    {
        $totals = ['calories' => 0, 'protein' => 0, 'carbs' => 0, 'fat' => 0];

        foreach ($this->ingredients as $ingredient) {
            $factor = $ingredient->pivot->quantity / $ingredient->ref_amount;

            $totals['calories'] += $ingredient->calories * $factor;
            $totals['protein']  += $ingredient->protein * $factor;
            $totals['carbs']    += $ingredient->carbs * $factor;
            $totals['fat']      += $ingredient->fat * $factor;
        }

        return array_map(fn($v) => round($v, 2), $totals);
    }

    public function recalculateAndSaveNutrients(): void
    {
        $this->load('ingredients');
        $nutrients = $this->calculateNutrients();
        $this->update($nutrients);
    }
}
