<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('maintenance', function (Blueprint $table) {
      $table->id();
      $table->foreignId('fishpond_id')->constrained('fishpond')->cascadeOnDelete();
      $table->string('description');
      $table->decimal('amount', 10);
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
    Schema::dropIfExists('maintenance');
  }
}
