<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_activity_reports', function (Blueprint $table) {
            $table->id();
            $table->string('task_name');
            $table->enum('activity_type', ['create', 'edit', 'delete', 'restore']);
            $table->string('user_name');
            $table->string('user_email');
            $table->string('ip_address');
            $table->timestamp('activity_date');
            $table->text('details')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_activity_reports');
    }
};
