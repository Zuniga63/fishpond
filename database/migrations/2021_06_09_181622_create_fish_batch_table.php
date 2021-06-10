<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFishBatchTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('fish_batch', function (Blueprint $table) {
      $table->id();
      $table->foreignId('fishpond_id')->nullable()->constrained('fishpond')->onDelete('SET NULL');
      $table->timestamp('seedtime');
      $table->timestamp('harvest')->nullable();
      $table->string('provider', 50)->nullable();
      $table->string('observation')->nullable();
      $table->unsignedSmallInteger('initial_population');   //{1 - 65535}
      $table->unsignedSmallInteger('population');           //{1 - 65535}
      $table->decimal('unit_price', 6, 2);                  //{1.00 - 9999.99}
      $table->float('initial_weight', 6, 2);                //{0.5g - 9999.99 g}
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
    Schema::dropIfExists('fish_batch');
  }
}
