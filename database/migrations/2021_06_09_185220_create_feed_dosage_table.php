<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedDosageTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('feed_dosage', function (Blueprint $table) {
      $table->id();
      $table->foreignId('fish_food_id')->constrained('fish_food');
      $table->foreignId('fish_food_stock_id')->constrained('fish_food_stock');
      $table->foreignId('fish_batch_id')->constrained('fish_batch');
      $table->float('quantity', 11, 2);
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
    Schema::dropIfExists('feed_dosage');
  }
}
