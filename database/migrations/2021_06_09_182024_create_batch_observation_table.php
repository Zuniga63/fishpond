<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchObservationTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('batch_observation', function (Blueprint $table) {
      $table->id();
      $table->foreignId('fish_batch_id')->constrained('fish_batch')->cascadeOnDelete();
      $table->string('title', 50);
      $table->text('observation');
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
    Schema::dropIfExists('batch_observation');
  }
}
