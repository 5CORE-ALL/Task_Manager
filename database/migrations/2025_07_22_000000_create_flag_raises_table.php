<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flag_raises', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('given_by');
            $table->unsignedBigInteger('team_member_id');
            $table->text('description');
            $table->enum('flag_type', ['red', 'green']);
            $table->timestamps();

            $table->foreign('given_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('team_member_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flag_raises');
    }
};
