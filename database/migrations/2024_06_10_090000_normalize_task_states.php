<?php

use App\Enums\TaskState;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Brings the `tasks` table in line with the rest of the application.
 *
 * Before this migration the `state` column was an enum mixing three
 * different casings ('Pending', 'Completed', 'unfinished') and it had no
 * default value, which made every insert have to remember the exact string.
 * The column is now a plain string holding a `TaskState` value, with a
 * default of 'pending'.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Normalise the existing values before the column type changes.
        $normalisation = [
            'Pending' => TaskState::Pending->value,
            'pending' => TaskState::Pending->value,
            'Completed' => TaskState::Completed->value,
            'completed' => TaskState::Completed->value,
            'unfinished' => TaskState::Unfinished->value,
            'Unfinished' => TaskState::Unfinished->value,
        ];

        foreach ($normalisation as $old => $new) {
            DB::table('tasks')->where('state', $old)->update(['state' => $new]);
        }

        // 2. Replace the enum by a string column so the values stay readable
        //    and so that adding a new state does not require a new migration.
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('state')->default(TaskState::Pending->value)->change();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('state', ['Completed', 'Pending', 'unfinished'])
                ->default('Pending')
                ->change();
        });

        DB::table('tasks')->where('state', TaskState::Pending->value)->update(['state' => 'Pending']);
        DB::table('tasks')->where('state', TaskState::Completed->value)->update(['state' => 'Completed']);
        DB::table('tasks')->where('state', TaskState::Unfinished->value)->update(['state' => 'unfinished']);
    }
};
