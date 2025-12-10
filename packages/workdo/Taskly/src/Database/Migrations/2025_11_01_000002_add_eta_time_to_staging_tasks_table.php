<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEtaTimeToStagingTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('staging_tasks'))
        {
            Schema::table('staging_tasks', function (Blueprint $table) {
                if (!Schema::hasColumn('staging_tasks', 'eta_time')) {
                    $table->integer('eta_time')->nullable()->after('priority');
                }
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
        if(Schema::hasTable('staging_tasks'))
        {
            Schema::table('staging_tasks', function (Blueprint $table) {
                if (Schema::hasColumn('staging_tasks', 'eta_time')) {
                    $table->dropColumn('eta_time');
                }
            });
        }
    }
}

