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
        Schema::create('report_forms', function (Blueprint $table) {
            $table->id();
            $table->string('form_name');
            $table->string('category');
            $table->string('status');
            $table->string('owner_name');
            $table->string('form_link')->nullable();
            $table->string('report_link')->nullable();
            $table->boolean('enable_form_view')->default(true);
            $table->boolean('enable_report_view')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_forms');
    }
};
