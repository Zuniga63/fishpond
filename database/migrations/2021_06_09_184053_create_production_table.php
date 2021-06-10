<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('production', function (Blueprint $table) {
      $table->id();
      $table->foreignId('fish_batch_id')->constrained('fish_batch')->cascadeOnDelete();
      $table->unsignedSmallInteger('quantity');   //{1 - 65 535}
      $table->float('weight', 11, 2);             //{1.00 g - 999'999'999.99g} => {1.00g - 999 Ton} 
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
    Schema::dropIfExists('production');
  }
}
