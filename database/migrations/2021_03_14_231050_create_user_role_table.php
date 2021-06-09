<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRoleTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('user_role', function (Blueprint $table) {
      $table->foreignId('user_id')->constrained('user')->cascadeOnDelete();
      $table->foreignId('role_id')->constrained('role')->cascadeOnDelete();
      $table->boolean('state')->default(true);
      $table->primary(['user_id', 'role_id']);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('user_role');
  }
}
