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
        // Keep both employee_id and add user_id for Laravel session handler
        Schema::table('sessions', function (Blueprint $table) {
            // Add employee_id if it doesn't exist (for users table using employee_id as PK)
            if (!Schema::hasColumn('sessions', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('id');
                $table->index('employee_id');
            }
            // Also ensure user_id exists for Laravel session handler
            if (!Schema::hasColumn('sessions', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('employee_id');
                $table->index('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
