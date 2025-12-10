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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('name');
            $table->string('department');
            $table->string('email_address');
            $table->string('month');
            $table->decimal('sal_previous', 10, 2)->default(0);
            $table->decimal('increment', 10, 2)->default(0);
            $table->decimal('salary_current', 10, 2)->default(0);
            $table->integer('productive_hrs')->default(0);
            $table->decimal('incentive', 10, 2)->default(0);
            $table->decimal('payable', 10, 2)->default(0);
            $table->decimal('advance', 10, 2)->default(0);
            $table->decimal('total_payable', 10, 2)->default(0);
            $table->boolean('payment_done')->default(false);
            $table->unsignedBigInteger('workspace_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['workspace_id', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
