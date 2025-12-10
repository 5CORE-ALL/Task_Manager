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
         Schema::create('scheduler_error_reports', function (Blueprint $table) {
        $table->id();
        $table->boolean('no_issue_found')->default(false);
        $table->boolean('issue_found_and_fixed')->default(false);
        $table->boolean('corrective_action_applied')->default(false);
        $table->text('remarks')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduler_error_reports');
    }
};
