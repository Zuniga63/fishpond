<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMortalityTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('mortality', function (Blueprint $table) {
      $table->id();
      $table->foreignId('fish_batch_id')->constrained('fish_batch')->cascadeOnDelete();
      $table->unsignedSmallInteger('deaths');       //{1 - 65535}
      $table->unsignedSmallInteger('population');   //{1 - 65535}
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
    Schema::dropIfExists('mortality');
  }
}
