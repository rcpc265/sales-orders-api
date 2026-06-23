<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite in tests, we cannot easily DROP CONSTRAINT and ADD CONSTRAINT without rebuilding the table.
        // However, for generic SQL compatibility, we can run raw SQL.
        // If it's SQLite, altering constraints is complicated, but since we're mostly likely using PostgreSQL/MySQL:
        // Actually, let's just make it generic. Wait, Laravel SQLite driver doesn't support dropping check constraints directly.
        // Since we are early in the project and might use SQLite for tests, we can just swallow the error if driver is sqlite, 
        // or just ignore the check constraint for sqlite.

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS check_status');
            DB::statement("ALTER TABLE orders ADD CONSTRAINT check_status CHECK (status IN ('pending', 'confirmed', 'shipped', 'delivered', 'cancelled'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS check_status');
            DB::statement("ALTER TABLE orders ADD CONSTRAINT check_status CHECK (status IN ('pending', 'completed', 'cancelled'))");
        }
    }
};
