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
        Schema::create('incentives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('employee_name');
            $table->string('department');
            $table->string('incentive_month');
            $table->decimal('requested_incentive', 10, 2);
            $table->text('incentive_reason');
            $table->decimal('approved_incentive', 10, 2)->nullable();
            $table->text('approval_reason')->nullable();
            $table->string('approved_by')->nullable();
            $table->date('review_date')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->string('workspace')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index(['workspace', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incentives');
    }
};
