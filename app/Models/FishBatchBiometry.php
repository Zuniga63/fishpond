<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishBatchBiometry extends Model
{
  use HasFactory;
  protected $table = 'fish_batch_biometry';
  protected $fillable = ['fish_batch_id', 'biometry_date', 'measurements'];
  protected $guarded = ['id'];

  protected $casts = [
    'measurements' => 'array'
  ];

  public function fishBatch()
  {
    return $this->belongsTo(FishBatch::class);
  }
}
