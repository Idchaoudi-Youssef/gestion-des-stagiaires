<?php

use App\Enums\UserType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A self-registered account must not be an intern straight away.
 *
 * The column used to default to 'intern', which meant that anybody able to
 * reach the registration form obtained the intern dashboard immediately.
 * The default is now 'pending': the account exists, it can sign in, but an
 * administrator has to give it a real role first.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('usertype')->default(UserType::Pending->value)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('usertype')->default(UserType::Intern->value)->change();
        });
    }
};
