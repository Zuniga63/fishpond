<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $now = Carbon::now();
    $permissions = [
      /**
       * PERMISOS PARA LOS USUARIOS
       */
      ['name' => 'Crear Usuarios', 'action' => 'create_user', 'order' => 1],
      ['name' => 'Ver Usuarios', 'action' => 'read_user', 'order' => 2],
      ['name' => 'Actualizar Usuarios', 'action' => 'update_user', 'order' => 3],
      ['name' => 'Eliminar Usuarios', 'action' => 'delete_user', 'order' => 4],
      ['name' => 'Cerrar Sessiones', 'action' => 'close_user_session', 'order' => 5],
      /**
       * PERMISOS PARA LOS PERMISOS
       */
      ['name' => 'Crear Permisos', 'action' => 'create_permission', 'order' => 6],
      ['name' => 'Ver Permisos', 'action' => 'read_permission', 'order' => 7],
      ['name' => 'Actualizar Permisos', 'action' => 'update_permission', 'order' => 8],
      ['name' => 'Eliminar Permisos', 'action' => 'delete_permission', 'order' => 9],
      ['name' => 'Asignar Permisos', 'action' => 'assing_permission', 'order' => 10],
      /**
       * PERMISOS PARA ADMINISTRAR ROLES
       */
      ['name' => 'Crear Roles', 'action' => 'create_role', 'order' => 11],
      ['name' => 'Ver Roles', 'action' => 'read_role', 'order' => 12],
      ['name' => 'Actualizar Roles', 'action' => 'update_role', 'order' => 13],
      ['name' => 'Eliminar Roles', 'action' => 'delete_role', 'order' => 14],
      ['name' => 'Asignar Roles', 'action' => 'assing_role', 'order' => 15],
      ['name' => 'Habilitar Roles', 'action' => 'enable_role', 'order' => 16],

      /**
       * PERMISOS PARA ADMINISTRAR LOS MENUS
       */
      ['name' => 'Crear Menús', 'action' => 'create_menu', 'order' => 17],
      ['name' => 'Ver Menús', 'action' => 'read_menu', 'order' => 18],
      ['name' => 'Actualizar Menús', 'action' => 'update_menu', 'order' => 19],
      ['name' => 'Eliminar Menús', 'action' => 'delete_menu', 'order' => 20],
      ['name' => 'Asignar Menus', 'action' => 'assign_menu', 'order' => 21],
      ['name' => 'Ordenar Menus', 'action' => 'order_menu', 'order' => 22],

      /**
       * PERMISOS PARA ADMINISTRAR LOS ESTANQUES
       */
      ["name" => "Crear Nuevo Estanque", "action" => "create_fishpond", "order" => 23],
      ["name" => "Actualizar Estanque", "action" => "update_fishpond", "order" => 24],
      ["name" => "Crear Costos a estanques", "action" => "create_fishpond_cost", "order" => 25],
      ["name" => "Actualiza Cotos del Estanque", "action" => "update_fishpond_cost", "order" => 26],
      ["name" => "Eliminar Estanque", "action" => "delete_fishpond", "order" => 27],
      ["name" => "Eliminar Costo de Estanque", "action" => "delete_fishpond_cost", "order" => 28],

      /**
       * PERMISOS PARA ADMINISTRAR LOTES
       */
      ["name" => "Crear Lote de Peces", "action" => "create_fish_batch", "order" => 29],
      ["name" => "Actualizar Lote de Peces", "action" => "update_fish_batch", "order" => 30],
      ["name" => "Eliminar Lote de Peces", "action" => "delete_fish_batch", "order" => 31],
    ];

    foreach ($permissions as $data) {
      $id = DB::table('permission')->insertGetId([
        'name'        => $data['name'],
        'action'      => $data['action'],
        'order'       => $data['order'],
        'created_at'  => $now,
        'updated_at'  => $now
      ]);

      /**
       * Ahora se asigna el permiso al super admin
       */
      DB::table('role_permission')->insert([
        'role_id' => 1,
        'permission_id' => $id
      ]);
    }
  } //.end run()
}
