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
        Schema::table('payrolls', function (Blueprint $table) {
            $table->integer('number_of_blogs_videos')->default(0)->after('productive_hrs');
            $table->decimal('rate', 10, 2)->default(0)->after('number_of_blogs_videos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['number_of_blogs_videos', 'rate']);
        });
    }
};
