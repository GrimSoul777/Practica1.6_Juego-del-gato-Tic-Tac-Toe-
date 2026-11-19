<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users ALTER COLUMN rol SET DEFAULT 'Jugador'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users ALTER COLUMN rol SET DEFAULT 'estudiante'");
    }
};