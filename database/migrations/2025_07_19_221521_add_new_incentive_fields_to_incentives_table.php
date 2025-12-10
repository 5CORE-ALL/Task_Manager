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
        Schema::table('incentives', function (Blueprint $table) {
            // Add new fields for the incentive system
            $table->unsignedBigInteger('giver_id')->nullable()->after('id');
            $table->unsignedBigInteger('receiver_id')->nullable()->after('giver_id');
            $table->decimal('amount', 10, 2)->nullable()->after('receiver_id');
            $table->date('start_date')->nullable()->after('amount');
            $table->date('end_date')->nullable()->after('start_date');
            $table->text('description')->nullable()->after('end_date');
            $table->unsignedBigInteger('workspace_id')->nullable()->after('workspace');
            
            // Add foreign key constraints
            $table->foreign('giver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incentives', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['giver_id']);
            $table->dropForeign(['receiver_id']);
            
            // Drop columns
            $table->dropColumn([
                'giver_id',
                'receiver_id', 
                'amount',
                'start_date',
                'end_date',
                'description',
                'workspace_id'
            ]);
        });
    }
};
