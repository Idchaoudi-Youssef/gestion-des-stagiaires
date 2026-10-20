<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The internship certificate mentions the position held by the intern, so
 * the column has to exist.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('internships', function (Blueprint $table) {
            $table->string('position')->nullable()->after('internship_period');
        });
    }

    public function down(): void
    {
        Schema::table('internships', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
