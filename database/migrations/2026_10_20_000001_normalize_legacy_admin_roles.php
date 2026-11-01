<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Convert existing legacy admin accounts without touching standard users. */
    public function up(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            DB::table('users')->where('role', 'admin')->update(['role' => 'super_admin']);
        }
    }

    /** Restore the legacy value if this migration is rolled back. */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            DB::table('users')->where('role', 'super_admin')->update(['role' => 'admin']);
        }
    }
};
