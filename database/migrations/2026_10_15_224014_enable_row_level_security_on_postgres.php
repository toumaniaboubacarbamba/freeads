<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ne concerne que PostgreSQL (production) : SQLite et les tests ne sont pas touchés.
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach (DB::select("select tablename from pg_tables where schemaname = 'public'") as $table) {
            DB::statement('ALTER TABLE "'.$table->tablename.'" ENABLE ROW LEVEL SECURITY');
        }
    }

    public function down(): void
    {
        // Volontairement vide : on ne désactive pas la sécurité.
    }
};
