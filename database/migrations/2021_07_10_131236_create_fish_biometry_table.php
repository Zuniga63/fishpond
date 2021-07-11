<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFishBiometryTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('fish_biometry', function (Blueprint $table) {
      $table->id();
      $table->foreignId('fish_batch_biometry_id')->constrained('fish_batch_biometry')->cascadeOnDelete();
      $table->float('weight', 5, 2); //{0.00 - 999.99} g
      $table->float('lenght', 4, 3); //{0.000 - 9.999} m 0.015
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
    Schema::dropIfExists('fish_biometry');
  }
}
