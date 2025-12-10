<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutomateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('automate_tasks'))
        {
            Schema::create('automate_tasks', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('title');
                $table->string('priority');
                $table->string('group')->nullable();
                $table->text('description')->nullable();
                $table->dateTime('start_date')->nullable();
                $table->dateTime('due_date')->nullable();
                $table->string('assign_to')->nullable();
                $table->string('assignor')->nullable();
                $table->string('link1')->nullable();
                $table->string('schedule_type')->nullable();
                $table->string('schedule_time')->nullable();
                $table->json('schedule_days')->nullable();
                $table->string('link2')->nullable();
                $table->string('status')->default('todo');
                $table->integer('order')->default(0);
                $table->integer('workspace');
                $table->timestamps();
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
        Schema::dropIfExists('task_automate');
    }
}
