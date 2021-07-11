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
      $table->string('name', 45);
      $table->string('brand', 20)->nullable();
      $table->string('description')->nullable();
      $table->charset = 'utf8mb4';
      $table->collation = 'utf8mb4_spanish_ci';
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
    Schema::dropIfExists('fish_food');
  }
}
