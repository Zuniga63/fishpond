<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    /**
     * Se crea los datos del administrador
     * por defecto
     */
    DB::table('user')->insert([
      'id'                => 1,
      'name'              => "Andrés Felipe Zúñiga",
      'email'             => "admin@admin.com",
      'email_verified_at'    => Carbon::now(),
      'password'          => Hash::make('admin'),
      'created_at'        => Carbon::now(),
      'updated_at'        => Carbon::now(),
    ]);

    /**
     * Se asigna el rol de administrador
     */
    DB::table('user_role')->insert([
      'user_id' => 1,
      'role_id' => 1,
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);
  }
}
