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
                    ->withTimestamps();
    }
}
