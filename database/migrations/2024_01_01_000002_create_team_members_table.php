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
        if (!Schema::hasTable('team_members')) {
            Schema::create('team_members', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('team_id');
                $table->integer('member_id'); // user_id of the team member
                $table->timestamps();
                
                $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
                $table->unique(['team_id', 'member_id']); // A member can only be added once per team
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
