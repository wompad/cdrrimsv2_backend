<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Modify the updated_at column to auto-update its timestamp
        DB::statement('
            ALTER TABLE tbl_disaster_reports
            ALTER COLUMN updated_at
            SET DEFAULT CURRENT_TIMESTAMP;
        ');

        DB::statement('
            CREATE OR REPLACE FUNCTION update_updated_at_column()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = NOW();
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');

        DB::statement('
            CREATE TRIGGER update_updated_at
            BEFORE UPDATE ON tbl_disaster_reports
            FOR EACH ROW
            EXECUTE PROCEDURE update_updated_at_column();
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the trigger and function
        DB::statement('DROP TRIGGER IF EXISTS update_updated_at ON tbl_disaster_reports;');
        DB::statement('DROP FUNCTION IF EXISTS update_updated_at_column;');

        // Optionally, you can revert the updated_at column back to its original definition
        DB::statement('
            ALTER TABLE tbl_disaster_reports
            ALTER COLUMN updated_at
            SET DEFAULT NULL;
        ');
    }
};
