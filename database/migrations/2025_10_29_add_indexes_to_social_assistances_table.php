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
        Schema::table('social_assistances', function (Blueprint $table) {
            // Tambahkan index pada kolom yang sering di-search untuk performa lebih baik
            $table->index('name');
            $table->index('category');
            $table->index('provider');
            $table->index('is_available');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_assistances', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['name']);
            $table->dropIndex(['category']);
            $table->dropIndex(['provider']);
            $table->dropIndex(['is_available']);
            $table->dropIndex(['created_at']);
        });
    }
};

