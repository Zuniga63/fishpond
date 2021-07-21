<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFishFoodTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('fish_food', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->nullable()->constrained('user')->onDelete('SET NULL');
      $table->string('name', 50);
      $table->string('brand', 50)->nullable();
      $table->enum('stage', ['initiation', 'growth', 'grow-fat', 'ending']);
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
    Schema::dropIfExists('fish_food');
  }
}
