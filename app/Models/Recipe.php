<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
   protected $fillable = [
    'name',
    'calories',
    'protein',
    'carbs',
    'fat',
    'prep_time'
];


public function users()
{
    return $this->belongsToMany(User::class, 'user_recipe')
                ->withPivot('date', 'meal_type')
                ->withTimestamps();
}

public function ingredients()
{
    return $this->belongsToMany(Ingredient::class, 'ingredient_recipe')
                ->withTimestamps();
}
}
