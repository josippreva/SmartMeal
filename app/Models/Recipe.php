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
        'prep_time',
        'user_id',
    ];

    // AUTOR RECEPTA
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // SASTOJCI
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'ingredient_recipe')
                    ->withTimestamps();
    }
}
