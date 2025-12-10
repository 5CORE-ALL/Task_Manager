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
        Schema::create('dar_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dar_id');
            $table->text('description');
            $table->integer('time_spent'); // in minutes
            $table->enum('status', ['Complete', 'Pending', 'In Progress']);
            $table->timestamps();
            
            $table->foreign('dar_id')->references('id')->on('dars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dar_tasks');
    }
};
