<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('missed_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->string('title');
            $table->string('task_type')->nullable();
            $table->string('schedule_type')->nullable();
            $table->timestamp('missed_at');
            $table->unsignedBigInteger('workspace');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('missed_tasks');
    }
};
