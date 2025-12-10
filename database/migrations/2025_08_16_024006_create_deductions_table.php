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
        Schema::create('deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('giver_id'); // Who applied the deduction
            $table->unsignedBigInteger('receiver_id'); // Who receives the deduction
            $table->decimal('amount', 10, 2); // Deduction amount
            $table->date('deduction_date'); // Date of deduction
            $table->text('description'); // Reason for deduction
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->enum('notification_status', ['unread', 'read'])->default('unread');
            $table->unsignedBigInteger('workspace_id')->default(1);
            $table->unsignedBigInteger('created_by');
            
            // Backward compatibility fields
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('employee_name')->nullable();
            $table->string('department')->nullable();
            $table->string('deduction_month')->nullable();
            $table->decimal('requested_deduction', 10, 2)->nullable();
            $table->text('deduction_reason')->nullable();
            $table->decimal('approved_deduction', 10, 2)->nullable();
            $table->text('approval_reason')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->date('review_date')->nullable();
            $table->string('workspace')->default('default');
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('giver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['receiver_id', 'status']);
            $table->index(['giver_id']);
            $table->index('deduction_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deductions');
    }
};
