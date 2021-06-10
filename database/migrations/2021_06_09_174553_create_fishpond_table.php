<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFishpondTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('fishpond', function (Blueprint $table) {
      $table->id();
      $table->string('name', 50);
      $table->float('volume')->nullable();                    //[m3]
      $table->float('area')->nullable();                      //[m2]
      $table->unsignedSmallInteger('capacity')->nullable();
      $table->boolean('in_use')->default(false);
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
    Schema::dropIfExists('fishpond');
  }
}
