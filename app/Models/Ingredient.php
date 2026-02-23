<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'ref_amount',
        'calories',
        'protein',
        'carbs',
        'fat',
    ];

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'ingredient_recipe')
                    ->withTimestamps();
    }
}
