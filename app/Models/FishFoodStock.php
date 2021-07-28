<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishFoodStock extends Model
{
  use HasFactory;
  protected $table = 'fish_food_stock';
  protected $fillable = ['fish_food_id', 'initial_stock', 'stock', 'amount'];
  protected $guarded = ['id'];

  public function fishFood()
  {
    $this->belongsTo(FishFood::class);
  }

  public function rations()
  {
    return $this->hasMany(FishFoodRation::class, 'fish_food_stock_id');
  }
}
