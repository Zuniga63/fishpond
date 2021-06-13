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
      $table->foreignId('user_id')->nullable()->constrained('user')->onDelete('SET NULL');
      $table->string('name', 20);
      $table->enum('type', ['circular', 'rectangular'])->default('rectangular');
      $table->unsignedFloat('width', 5, 2)->nullable();                            //[m] {0.01 - 999.99}
      $table->unsignedFloat('long', 5, 2)->nullable();                             //[m] {0.01 - 999.99}
      $table->unsignedFloat('max_height', 3, 2)->nullable();                       //[m] {0.01 - 9.99}
      $table->unsignedFloat('effective_height', 3, 2)->nullable();                 //[m] {0.01 - 9.99}
      $table->unsignedFloat('diameter', 5, 2)->nullable();                            //[m] {0.01 - 999.99}
      $table->unsignedSmallInteger('capacity')->nullable();                        //[und] {0 - 65535}
      $table->boolean('in_use')->default(false);
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
    Schema::dropIfExists('fishpond');
  }
}
