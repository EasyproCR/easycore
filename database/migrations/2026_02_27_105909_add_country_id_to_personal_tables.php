<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $tables = [
        'acces_requests',
        'ad_requests',
        'billing_controls',
        'campaigns',
        'collaboration_requests',
        'credit_study_requests',
        'customer_reports',
        'leave_requests',
        'offers',
        'personal_customers',
        'projects',
        'property_assignments'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'country_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'country_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['country_id']);
                    $table->dropColumn('country_id');
                });
            }
        }
    }
};
