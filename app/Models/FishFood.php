<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishFood extends Model
{
  use HasFactory;
  protected $table = 'fish_food';
  protected $fillable = ['user_id', 'name', 'brand', 'stage'];
  protected $guarded = ['id'];

  public function stocks()
  {
    return $this->hasMany(FishFoodStock::class, 'fish_food_id');
  }

  public function rations()
  {
    return $this->hasMany(FishFoodRation::class, 'fish_food_id');
  }
}
