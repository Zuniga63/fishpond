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
      $table->foreignId('biometry_id')->constrained('biometry')->cascadeOnDelete();
      $table->float('weight', 6, 2);              //{1.00g - 9999.99}
      $table->float('length', 5, 4)->nullable();  //{0.0001 m - 9.9999 m}
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
