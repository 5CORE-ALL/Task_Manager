<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEtcAtcColumnsToPayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('etc_hours', 8, 1)->default(0)->after('productive_hrs')->comment('Estimated Time to Complete in hours');
            $table->decimal('atc_hours', 8, 1)->default(0)->after('etc_hours')->comment('Actual Time to Complete in hours');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['etc_hours', 'atc_hours']);
        });
    }
}
