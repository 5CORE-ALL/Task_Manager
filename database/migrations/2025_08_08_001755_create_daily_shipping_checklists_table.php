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
        Schema::create('daily_shipping_checklists', function (Blueprint $table) {
            $table->id();
            $table->string('user_name');
            $table->date('checklist_date');
            $table->enum('task_1', ['Yes', 'No'])->comment('Were all new orders given to Dispatch on time at 3PM?');
            $table->text('task_1_comments')->nullable();
            $table->enum('task_2', ['Yes', 'No'])->comment('Were any labels printed for cancelled ORDERS Today?');
            $table->text('task_2_comments')->nullable();
            $table->enum('task_3', ['Yes', 'No'])->default('Yes')->comment('If yes then - Were cancelled ORDER labels properly voided/refunded?');
            $table->text('task_3_comments')->nullable();
            $table->enum('task_4', ['Yes', 'No'])->comment('Verified that 20 labels checked randomly were with proper weight and cost?');
            $table->text('task_4_comments')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            
            // Add index for better performance
            $table->index(['user_id', 'checklist_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_shipping_checklists');
    }
};
