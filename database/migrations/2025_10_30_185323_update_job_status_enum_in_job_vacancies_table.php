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
    public function up(): void
    {
        // Temporarily change enum to VARCHAR to allow any value
        DB::statement("ALTER TABLE job_vacancies MODIFY COLUMN job_status VARCHAR(255) NOT NULL");
        
        // Migrate existing data from old enum values to new ones
        // 'ongoing' -> 'open', 'completed' -> 'closed'
        DB::table('job_vacancies')
            ->where('job_status', 'ongoing')
            ->update(['job_status' => 'open']);
        
        DB::table('job_vacancies')
            ->where('job_status', 'completed')
            ->update(['job_status' => 'closed']);
        
        // Now update to new enum values
        DB::statement("ALTER TABLE job_vacancies MODIFY COLUMN job_status ENUM('open', 'closed', 'filled') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Temporarily change enum to VARCHAR to allow any value
        DB::statement("ALTER TABLE job_vacancies MODIFY COLUMN job_status VARCHAR(255) NOT NULL");
        
        // Migrate data back from new enum values to old ones
        // 'open' -> 'ongoing', 'closed' -> 'completed'
        DB::table('job_vacancies')
            ->where('job_status', 'open')
            ->update(['job_status' => 'ongoing']);
        
        DB::table('job_vacancies')
            ->where('job_status', 'closed')
            ->update(['job_status' => 'completed']);
        
        // Revert back to original enum values
        DB::statement("ALTER TABLE job_vacancies MODIFY COLUMN job_status ENUM('ongoing', 'completed') NOT NULL");
    }
};
