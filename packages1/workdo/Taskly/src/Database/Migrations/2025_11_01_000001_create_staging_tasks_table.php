<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStagingTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('staging_tasks'))
        {
            Schema::create('staging_tasks', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('event_id');
                $table->unsignedBigInteger('user_id');
                $table->string('group')->nullable();
                $table->string('task');
                $table->unsignedBigInteger('assignor_id')->nullable();
                $table->unsignedBigInteger('assignee_id')->nullable();
                $table->dateTime('start_date')->nullable();
                $table->dateTime('end_date')->nullable();
                $table->string('status')->default('pending');
                $table->string('priority')->default('medium');
                $table->text('task_note')->nullable();
                $table->string('l1')->nullable();
                $table->string('l2')->nullable();
                $table->string('l3')->nullable();
                $table->string('l4')->nullable();
                $table->string('l5')->nullable();
                $table->string('l6')->nullable();
                $table->string('l7')->nullable();
                $table->timestamps();
                
                // Foreign key constraints
                $table->foreign('event_id')->references('id')->on('stagings')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('assignor_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('assignee_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staging_tasks');
    }
}

