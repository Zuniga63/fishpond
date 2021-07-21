<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishFoodRation extends Model
{
  use HasFactory;
  protected $table = 'fish_food_ration';
  protected $fillable = ['fish_food_id', 'fish_batch_id', 'fish_food_stock_id', 'quantity'];
  protected $guarded = ['id'];

  public function fishFood()
  {
    $this->belongsTo(FishFood::class);
  }
}
