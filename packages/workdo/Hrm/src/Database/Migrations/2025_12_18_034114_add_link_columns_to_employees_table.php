<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'training')) {
                $table->string('training')->after('zipcode')->nullable();
            }
            
            if (!Schema::hasColumn('employees', 'sop_guidelines')) {
                $table->string('sop_guidelines')->after('training')->nullable();
            }
            
            if (!Schema::hasColumn('employees', 'reports')) {
                $table->string('reports')->after('sop_guidelines')->nullable();
            }
            
            if (!Schema::hasColumn('employees', 'kpi')) {
                $table->string('kpi')->after('reports')->nullable();
            }
            
            if (!Schema::hasColumn('employees', 'checklist_rr')) {
                $table->string('checklist_rr')->after('kpi')->nullable();
            }
            
            if (!Schema::hasColumn('employees', 'checklist_sr_rr')) {
                $table->string('checklist_sr_rr')->after('checklist_rr')->nullable();
            }
            
            if (!Schema::hasColumn('employees', 'checklist_general')) {
                $table->string('checklist_general')->after('checklist_sr_rr')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'training')) {
                $table->dropColumn('training');
            }
            if (Schema::hasColumn('employees', 'sop_guidelines')) {
                $table->dropColumn('sop_guidelines');
            }
            if (Schema::hasColumn('employees', 'reports')) {
                $table->dropColumn('reports');
            }
            if (Schema::hasColumn('employees', 'kpi')) {
                $table->dropColumn('kpi');
            }
            if (Schema::hasColumn('employees', 'checklist_rr')) {
                $table->dropColumn('checklist_rr');
            }
            if (Schema::hasColumn('employees', 'checklist_sr_rr')) {
                $table->dropColumn('checklist_sr_rr');
            }
            if (Schema::hasColumn('employees', 'checklist_general')) {
                $table->dropColumn('checklist_general');
            }
        });
    }
};
