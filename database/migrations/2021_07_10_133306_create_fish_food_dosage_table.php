<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFishFoodDosageTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('fish_food_dosage', function (Blueprint $table) {
      $table->id();
      $table->foreignId('fish_food_id')->constrained('fish_food');
      $table->foreignId('fish_batch_id')->constrained('fish_batch');
      $table->foreignId('fish_food_stock_id')->constrained('fish_food_stock');
      $table->unsignedMediumInteger('quantity'); //{0 - 16'777'215}
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
    Schema::dropIfExists('fish_food_dosage');
  }
}
