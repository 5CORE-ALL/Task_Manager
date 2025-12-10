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
        // Check if table exists first
        if (!Schema::hasTable('incentives')) {
            Schema::create('incentives', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('giver_id')->nullable();
                $table->unsignedBigInteger('receiver_id')->nullable();
                $table->decimal('amount', 10, 2)->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->text('description')->nullable();
                $table->string('status')->default('active');
                $table->string('notification_status')->default('pending');
                $table->unsignedBigInteger('workspace_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                
                // Keep existing fields for backward compatibility
                $table->unsignedBigInteger('employee_id')->nullable();
                $table->string('employee_name')->nullable();
                $table->string('department')->nullable();
                $table->string('incentive_month')->nullable();
                $table->decimal('requested_incentive', 10, 2)->nullable();
                $table->text('incentive_reason')->nullable();
                $table->decimal('approved_incentive', 10, 2)->nullable();
                $table->text('approval_reason')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->date('review_date')->nullable();
                $table->string('workspace')->nullable();
                
                $table->timestamps();
                
                // Foreign key constraints
                $table->foreign('giver_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('receiver_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            });
        } else {
            // Add new columns if they don't exist
            Schema::table('incentives', function (Blueprint $table) {
                if (!Schema::hasColumn('incentives', 'giver_id')) {
                    $table->unsignedBigInteger('giver_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('incentives', 'receiver_id')) {
                    $table->unsignedBigInteger('receiver_id')->nullable()->after('giver_id');
                }
                if (!Schema::hasColumn('incentives', 'amount')) {
                    $table->decimal('amount', 10, 2)->nullable()->after('receiver_id');
                }
                if (!Schema::hasColumn('incentives', 'start_date')) {
                    $table->date('start_date')->nullable()->after('amount');
                }
                if (!Schema::hasColumn('incentives', 'end_date')) {
                    $table->date('end_date')->nullable()->after('start_date');
                }
                if (!Schema::hasColumn('incentives', 'description')) {
                    $table->text('description')->nullable()->after('end_date');
                }
                if (!Schema::hasColumn('incentives', 'status')) {
                    $table->string('status')->default('active')->after('description');
                }
                if (!Schema::hasColumn('incentives', 'notification_status')) {
                    $table->string('notification_status')->default('pending')->after('status');
                }
                if (!Schema::hasColumn('incentives', 'workspace_id')) {
                    $table->unsignedBigInteger('workspace_id')->nullable()->after('notification_status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incentives');
    }
};
