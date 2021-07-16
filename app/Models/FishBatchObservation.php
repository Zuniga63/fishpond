<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishBatchObservation extends Model
{
  use HasFactory;
  protected $table = 'fish_batch_observation';
  protected $fillable = ['fish_batch_id', 'title', 'message'];
  protected $guarded = ['id'];

  public function fishBatch()
  {
    return $this->belongsTo(FishBatch::class);
  }
}
