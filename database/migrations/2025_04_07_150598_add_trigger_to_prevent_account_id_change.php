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
        DB::unprepared('
            CREATE TRIGGER prevent_account_id_change
            BEFORE UPDATE OF account_id ON users
            FOR EACH ROW
            WHEN OLD.account_id IS NOT NULL AND NEW.account_id != OLD.account_id
            BEGIN
                SELECT RAISE(ABORT, "Não é possível alterar o account_id uma vez definido");
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS prevent_account_id_change');
    }
}; 