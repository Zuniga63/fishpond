<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBiometryTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('biometry', function (Blueprint $table) {
      $table->id();
      $table->foreignId('fish_batch_id')->constrained('fish_batch')->cascadeOnDelete();
      $table->timestamp('biometry_date');
      $table->unsignedSmallInteger('population');   //{1 - 65535}
      $table->unsignedTinyInteger('sample_size');   //{1 - 255}
      $table->float('average_weight', 6, 2);        //{1.00g - 9999.99g}
      $table->float('biomass', 11, 2);              //{1.00g - 999'999'999.99 }
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
    Schema::dropIfExists('biometry');
  }
}
