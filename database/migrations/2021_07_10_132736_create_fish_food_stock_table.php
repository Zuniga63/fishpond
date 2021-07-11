<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFishFoodStockTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('fish_food_stock', function (Blueprint $table) {
      $table->id();
      $table->foreignId('fish_food_id')->constrained('fish_food');
      $table->unsignedMediumInteger('quantity'); //{0 - 16'777'215}
      $table->unsignedMediumInteger('stock'); //{0 - 16'777'215}
      $table->float('amount', 10, 2);
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
    Schema::dropIfExists('fish_food_stock');
  }
}
