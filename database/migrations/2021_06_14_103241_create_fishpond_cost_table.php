<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFishpondCostTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('fishpond_cost', function (Blueprint $table) {
      $table->id();
      $table->foreignId('fishpond_id')->constrained('fishpond')->cascadeOnDelete();
      $table->timestamp('cost_date');
      $table->enum('type', ['materials', 'workforce', 'maintenance']);
      $table->string('description');
      $table->decimal('amount', 10, 2);  //{0.01 - 99'999'999.99}
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
    Schema::dropIfExists('fishpond_cost');
  }
}
