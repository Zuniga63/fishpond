<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishBatch extends Model
{
  use HasFactory;

  protected $table = 'fish_batch';
  protected $fillable = ['user_id', 'fishpond_id', 'seedtime', 'initial_population', 'initial_weight', 'population', 'amount'];
  protected $guarded = ['id'];

  public function observations()
  {
    return $this->hasMany(FishBatchObservation::class, 'fish_batch_id');
  }

  public function expenses()
  {
    return $this->hasMany(FishBatchExpense::class, 'fish_batch_id');
  }

  
}
