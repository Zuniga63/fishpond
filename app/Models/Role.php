<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
  use HasFactory;
  
  protected $table = 'role';
  protected $fillable = ['name', 'slug', 'description'];
  protected $guarded = ['id'];

  //------------------------------------------
  // RELACIONES
  //------------------------------------------
  /**
   * Relaciona a un rol con todos los permisis
   * asociados a esta entidad
   */
  public function permissions()
  {
    return $this->belongsToMany(Permission::class, 'role_permission');
  }

  /**
   * Relaciona a este rol con todos los usuarios
   * independiente de su estado
   */
  public function users()
  {
    return $this->belongsToMany(User::class, 'user_role')->withPivot('state');
  }

  public function menus()
  {
    return $this->belongsToMany(Menu::class, 'role_menu');
  }
}
