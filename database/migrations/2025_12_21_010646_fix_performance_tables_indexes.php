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
        // Drop tables if they exist (they were created without proper indexes)
        Schema::dropIfExists('performance_feedback');
        Schema::dropIfExists('performance_management');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to reverse - we're just cleaning up
    }
};
