<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
  use HasFactory;
  protected $table = 'permission';
  protected $fillable = ['name', 'action', 'order'];
  protected $guarded = ['id'];

  public function roles()
  {
    return $this->belongsToMany(Role::class, 'role_permission');
  }
}
