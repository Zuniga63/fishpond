<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishFoodStock extends Model
{
  use HasFactory;
  protected $table = 'fish_food_stock';
  protected $fillable = ['fish_food_id', 'quantity', 'stock', 'amount'];
  protected $guarded = ['id'];

  public function fishFood()
  {
    $this->belongsTo(FishFood::class);
  }
}
