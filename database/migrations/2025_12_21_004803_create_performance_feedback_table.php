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
        Schema::create('performance_feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id'); // User ID of the employee being evaluated
            $table->unsignedBigInteger('performance_management_id')->nullable(); // Link to performance_management record
            $table->unsignedBigInteger('given_by'); // User ID of senior management giving feedback
            $table->string('period_type', 20)->default('monthly'); // monthly, weekly, quarterly, yearly
            $table->string('period', 20); // e.g., "2025-01"
            $table->date('feedback_date');
            
            // Custom feedback parameters (scored 0-100)
            $table->decimal('communication_skill', 5, 2)->nullable();
            $table->decimal('teamwork', 5, 2)->nullable();
            $table->decimal('problem_solving', 5, 2)->nullable();
            $table->decimal('initiative', 5, 2)->nullable();
            $table->decimal('quality_of_work', 5, 2)->nullable();
            $table->decimal('reliability', 5, 2)->nullable();
            $table->decimal('adaptability', 5, 2)->nullable();
            $table->decimal('leadership', 5, 2)->nullable();
            
            // Additional custom parameters (JSON for flexibility)
            $table->json('custom_parameters')->nullable(); // For any additional parameters
            
            // Feedback comments
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('general_feedback')->nullable();
            $table->text('goals')->nullable();
            
            $table->integer('workspace_id')->default(1);
            $table->timestamps();
            
            $table->index(['employee_id', 'period_type']);
            $table->index('period');
            $table->index('performance_management_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_feedback');
    }
};
