<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('job_vacancies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('thumbnail');
            $table->string('job_title');
            $table->longText('description');
            $table->string('company_in_charge');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('salary', 10, 2);
            $table->enum('job_status', ['ongoing', 'completed']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_vacancies');
    }
};
