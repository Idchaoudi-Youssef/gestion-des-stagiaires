<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Data integrity for the pivot table and for the internship relation.
 *
 * - `intern_supervisor` gets a unique index so the same intern cannot be
 *   attached twice to the same supervisor.
 * - `internships.intern_id` gets a unique index so an intern always has at
 *   most one internship record (the application used to create duplicates
 *   every time an intern was edited).
 * - `users.department_id` no longer cascades: deleting a department used to
 *   silently delete every user of that department. The users are now kept
 *   and simply stop being attached to a department.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->removeDuplicatedPivotRows();

        Schema::table('intern_supervisor', function (Blueprint $table) {
            $table->unique(['intern_id', 'supervisor_id'], 'intern_supervisor_unique');
        });

        $this->removeDuplicatedInternships();

        Schema::table('internships', function (Blueprint $table) {
            $table->unique('intern_id', 'internships_intern_id_unique');
        });

        $this->detachDepartmentsInsteadOfDeletingUsers();
    }

    public function down(): void
    {
        Schema::table('internships', function (Blueprint $table) {
            $table->dropUnique('internships_intern_id_unique');
        });

        Schema::table('intern_supervisor', function (Blueprint $table) {
            $table->dropUnique('intern_supervisor_unique');
        });
    }

    /**
     * Keep only the first row of each duplicated pair.
     */
    protected function removeDuplicatedPivotRows(): void
    {
        $duplicates = DB::table('intern_supervisor')
            ->selectRaw('intern_id, supervisor_id, MIN(id) as keep_id, COUNT(*) as total')
            ->groupBy('intern_id', 'supervisor_id')
            ->having('total', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('intern_supervisor')
                ->where('intern_id', $duplicate->intern_id)
                ->where('supervisor_id', $duplicate->supervisor_id)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }
    }

    /**
     * Keep only the most recent internship of each intern.
     */
    protected function removeDuplicatedInternships(): void
    {
        $keep = DB::table('internships')
            ->selectRaw('intern_id, MAX(id) as keep_id')
            ->groupBy('intern_id')
            ->pluck('keep_id', 'intern_id');

        DB::table('internships')
            ->whereNotIn('id', $keep->values())
            ->delete();
    }

    /**
     * Replace the "on delete cascade" of `users.department_id` by
     * "on delete set null".
     *
     * SQLite cannot alter a foreign key, so the table is rebuilt there. The
     * procedure follows the one recommended by SQLite: the constraints are
     * disabled, a new table is created, the rows are copied and the old table
     * is dropped. The children of `users` keep pointing at the name
     * "users", so they stay valid.
     */
    protected function detachDepartmentsInsteadOfDeletingUsers(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            $this->rebuildUsersTableForSqlite();

            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->nullOnDelete();
        });
    }

    /**
     * Rebuild the `users` table so the foreign key of `department_id` sets
     * the column to null instead of deleting the row.
     */
    protected function rebuildUsersTableForSqlite(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement(<<<'SQL'
            CREATE TABLE "users_new" (
                "id" integer not null primary key autoincrement,
                "name" varchar not null,
                "email" varchar not null,
                "usertype" varchar not null default 'intern',
                "phone" varchar null,
                "address" varchar null,
                "email_verified_at" timestamp null,
                "password" varchar not null,
                "remember_token" varchar null,
                "created_at" timestamp null,
                "updated_at" timestamp null,
                "department_id" integer null,
                constraint "users_department_id_foreign" foreign key ("department_id") references "departments" ("id") on delete set null on update cascade
            )
        SQL);

        DB::statement(<<<'SQL'
            INSERT INTO "users_new" (
                "id", "name", "email", "usertype", "phone", "address",
                "email_verified_at", "password", "remember_token",
                "created_at", "updated_at", "department_id"
            )
            SELECT
                "id", "name", "email", "usertype", "phone", "address",
                "email_verified_at", "password", "remember_token",
                "created_at", "updated_at", "department_id"
            FROM "users"
        SQL);

        DB::statement('DROP TABLE "users"');
        DB::statement('ALTER TABLE "users_new" RENAME TO "users"');

        // The indexes are dropped together with the old table.
        DB::statement('CREATE UNIQUE INDEX "users_email_unique" ON "users" ("email")');
        DB::statement('CREATE INDEX "users_department_id_index" ON "users" ("department_id")');

        DB::statement('PRAGMA foreign_keys = ON');
    }
};
