<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fishpond extends Model
{
  use HasFactory;
  protected $table = 'fishpond';
  protected $fillable = ['user_id', 'name', 'type', 'width', 'long', 'max_height', 'effective_height', 'diameter', 'capacity'];
  protected $guarded = ['id'];

  public function costs(){
    return $this->hasMany(FishpondCost::class);
  }
}
