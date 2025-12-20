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
        Schema::create('performance_management', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id'); // User ID
            $table->unsignedBigInteger('user_id'); // User ID (same as employee_id, kept for consistency)
            $table->string('period_type', 20)->default('monthly'); // monthly, weekly, quarterly, yearly
            $table->string('period', 20); // e.g., "2025-01", "2025-W01", "2025-Q1", "2025"
            $table->date('start_date');
            $table->date('end_date');
            
            // Metrics from existing data
            $table->decimal('etc_hours', 10, 2)->default(0); // Estimated Time to Complete (from tasks.eta_time)
            $table->decimal('atc_hours', 10, 2)->default(0); // Actual Time to Complete (from tasks.etc_done)
            $table->decimal('total_working_hours', 10, 2)->default(0); // From attendance
            $table->decimal('productive_hours', 10, 2)->default(0); // From DAR or calculated
            $table->integer('tasks_completed')->default(0); // Number of completed tasks from DAR
            $table->decimal('avg_task_duration_minutes', 10, 2)->default(0); // Average time to complete tasks
            $table->decimal('avg_task_duration_days', 10, 2)->default(0); // Average days to complete tasks
            $table->integer('total_tasks_assigned')->default(0); // Total tasks assigned in period
            $table->integer('total_tasks_completed')->default(0); // Total tasks completed in period
            $table->decimal('task_completion_rate', 5, 2)->default(0); // Percentage
            
            // Calculated scores (0-100)
            $table->decimal('efficiency_score', 5, 2)->default(0); // Based on ETC vs ATC
            $table->decimal('productivity_score', 5, 2)->default(0); // Based on productive hours
            $table->decimal('task_performance_score', 5, 2)->default(0); // Based on task completion
            $table->decimal('timeliness_score', 5, 2)->default(0); // Based on task duration
            $table->decimal('overall_score', 5, 2)->default(0); // Overall performance score
            
            $table->integer('workspace_id')->default(1);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->index(['employee_id', 'period_type']);
            $table->index('period');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_management');
    }
};
