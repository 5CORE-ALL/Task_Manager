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
        Schema::create('moms', function (Blueprint $table) {
            $table->id();
            $table->string('meeting_name');
            $table->date('meeting_date');
            $table->string('location');
            $table->string('host_name');
            $table->string('host_email');
            $table->text('assignees')->nullable();
            $table->longText('agenda');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->index(['host_email', 'meeting_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moms');
    }
};
