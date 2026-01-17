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
        Schema::create('daily_overdue_counts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('record_date');
            $table->integer('overdue_count');
            $table->unsignedBigInteger('workspace')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'record_date']);
            $table->index('record_date');
            $table->unique(['user_id', 'record_date', 'workspace'], 'unique_user_date_workspace');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_overdue_counts');
    }
};