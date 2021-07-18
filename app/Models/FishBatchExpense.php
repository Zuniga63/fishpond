<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishBatchExpense extends Model
{
  use HasFactory;
  protected $table = 'fish_batch_expense';
  protected $fillable = ['fish_batch_id', 'expense_date', 'description', 'amount'];
  protected $guarded = ['id'];

  public function fishBatch()
  {
    return $this->belongsTo(FishBatch::class);
  }
}
