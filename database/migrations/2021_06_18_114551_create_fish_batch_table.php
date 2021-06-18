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
      $table->foreignId('user_id')->nullable()->constrained('user')->onDelete('SET NULL');
      $table->foreignId('fishpond_id')->nullable()->constrained('fishpond')->onDelete('SET NULL');
      $table->string('provider', 20)->nullable();
      $table->string('observation')->nullable();
      $table->timestamp('seedtime');
      $table->timestamp('harvest')->nullable();
      $table->unsignedSmallInteger('initial_polulation');
      $table->float('initial_weight', 5, 2);
      $table->unsignedSmallInteger('population');
      $table->decimal('amount', 10, 2);
      $table->timestamps();
      $table->charset = 'utf8mb4';
      $table->collation = 'utf8mb4_spanish_ci';
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
