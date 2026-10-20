<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained('users')->onDelete('cascade');
            $table->date('start_date')->nullable(); // Make the start_date field nullable
            $table->date('end_date')->nullable(); // Make the end_date field nullable
            $table->string('status')->default('not_started');
            $table->integer('internship_period')->nullable();
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
        Schema::dropIfExists('internships');
    }
}
