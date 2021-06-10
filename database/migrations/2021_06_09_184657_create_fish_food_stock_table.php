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
      $table->float('quantity', 10, 2);     //{1.00g - 99'999'999.99 g}
      $table->float('stock', 10, 2);        //{1.00g - 99'999'999.99 g}
      $table->decimal('amount');
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
