<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('menu', function (Blueprint $table) {
      $table->id();
      $table->foreignId('father_id')->nullable()->constrained('menu')->cascadeOnDelete();
      $table->string('name', 50);
      $table->string('url', 100);
      $table->string('icon', 50)->nullable();
      $table->unsignedTinyInteger('order')->default(1);
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
    Schema::dropIfExists('menu');
  }
}
