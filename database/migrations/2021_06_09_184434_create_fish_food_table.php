<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFishFoodTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('fish_food', function (Blueprint $table) {
      $table->id();
      $table->string('name', 50);
      $table->string('presentation', 50)->nullable();
      $table->string('brand', 50)->nullable();
      $table->string('stage', 50)->nullable();
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
    Schema::dropIfExists('fish_food');
  }
}
