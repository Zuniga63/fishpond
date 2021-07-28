<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $menus = [
      ["id" => 1, "fatherId" => null, "name" => "Panel", "url" => "admin", "icon" => "fas fa-tachometer-alt", "order" => 1, ],
      ["id" => 10, "fatherId" => null, "name" => "Alimentos", "url" => "admin/alimentos", "icon" => "fas fa-cubes", "order" => 2, ],
      ["id" => 8, "fatherId" => null, "name" => "Estanques", "url" => "admin/estanques", "icon" => "fas fa-water", "order" => 4, ],
      ["id" => 9, "fatherId" => null, "name" => "Lote de Peces", "url" => "admin/lotes", "icon" => "fas fa-fish", "order" => 5, ],
      ["id" => 2, "fatherId" => null, "name" => "Administracion", "url" => "#", "icon" => "fas fa-cogs", "order" => 6, ],
        ["id" => 3, "fatherId" => 2, "name" => "Usuarios", "url" => "admin/usuarios", "icon" => "fas fa-users", "order" => 1, ],
        ["id" => 4, "fatherId" => 2, "name" => "Permisos", "url" => "admin/permisos", "icon" => "fas fa-hand-paper", "order" => 2, ],
        ["id" => 5, "fatherId" => 2, "name" => "Roles", "url" => "admin/roles", "icon" => "fas fa-user-tag", "order" => 3, ],
        ["id" => 7, "fatherId" => 2, "name" => "Menus", "url" => "admin/menus", "icon" => "fas fa-server", "order" => 4, ],
    ];

    foreach ($menus as $key => $menu) {
      DB::table('menu')->insert([
        'id' => $menu['id'],
        'father_id' => $menu['fatherId'],
        'order' => $menu['order'],
        'name' => $menu['name'],
        'url' => $menu['url'],
        'icon' => $menu['icon'],
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ]);

      DB::table('role_menu')->insert([
        'menu_id' => $menu['id'],
        'role_id' => 1
      ]);
    }
  } //.end run()
}
