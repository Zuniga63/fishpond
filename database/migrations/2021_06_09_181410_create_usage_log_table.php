<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsageLogTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('usage_log', function (Blueprint $table) {
      $table->id();
      $table->foreignId('fishpond_id')->constrained('fishpond')->cascadeOnDelete();
      $table->timestamp('start_production');
      $table->timestamp('end_production')->nullable();
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
    Schema::dropIfExists('usage_log');
  }
}
