<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchExpenseTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('batch_expense', function (Blueprint $table) {
      $table->id();
      $table->foreignId('fish_batch_id')->constrained('fish_batch')->cascadeOnDelete();
      $table->timestamp('expense_date');
      $table->string('description');
      $table->decimal('amount');
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
    Schema::dropIfExists('batch_expense');
  }
}
