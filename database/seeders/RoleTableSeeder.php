<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $roles = ['SuperAdmin', 'Administrador'];
    $now = Carbon::now();

    foreach ($roles as $role) {
      DB::table('role')->insert([
        'name' => $role,
        'slug' => strtolower($role),
        'created_at' => $now->format('Y-m-d H:i:s'),
        'updated_at' => $now->format('Y-m-d H:i:s'),
      ]);
    }
  }
}
