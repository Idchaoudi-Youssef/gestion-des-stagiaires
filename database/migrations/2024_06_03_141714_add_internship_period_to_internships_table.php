<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInternshipPeriodToInternshipsTable extends Migration
{
    public function up()
    {
        Schema::table('internships', function (Blueprint $table) {
            $table->integer('internship_period')->nullable(); // Add the new column
        });
    }

    public function down()
    {
        Schema::table('internships', function (Blueprint $table) {
            $table->dropColumn('internship_period'); // Drop the column if needed
        });
    }
}
