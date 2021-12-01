<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSprintsTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('sprints', function (Blueprint $table) {
      $table->id();
      $table->string('title', 100);
      $table->dateTime('start_date', 0);
      $table->dateTime('end_date', 0);
      $table->timestamps();
      $table->softDeletes('deleted_at', 0);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('sprints');
  }
}
