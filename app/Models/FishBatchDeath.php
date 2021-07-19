<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishBatchDeath extends Model
{
  use HasFactory;
  protected $table = 'fish_batch_death';
  protected $fillable = ['fish_batch_id', 'deaths',];
  protected $guarded = ['id'];

  public function fishBatch()
  {
    return $this->belongsTo(FishBatch::class);
  }
}
