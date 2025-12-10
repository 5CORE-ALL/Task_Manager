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
        Schema::create('done_clears', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignor_id');
            $table->string('assignor_name');
            $table->unsignedBigInteger('assignee_id');
            $table->string('assignee_name');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->unsignedBigInteger('created_by');
            $table->integer('workspace')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('assignor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assignee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index(['assignor_id', 'assignee_id']);
            $table->index('created_by');
            $table->index('workspace');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('done_clears');
    }
};
