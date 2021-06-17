<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishpondCost extends Model
{
  use HasFactory;
  protected $table = 'fishpond_cost';
  protected $fillable = ['fishpond_id', 'cost_date', 'type', 'description', 'amount'];
  protected $guarded = ['id'];

  public function fishpond()
  {
    return $this->belongsTo(Fishpond::class);
  }
}
