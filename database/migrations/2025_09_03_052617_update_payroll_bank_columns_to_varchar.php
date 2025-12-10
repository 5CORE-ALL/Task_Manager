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
            // Change bank1 and bank2 columns from decimal to varchar for account numbers
            $table->string('bank1')->nullable()->change();
            $table->string('bank2')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Revert back to decimal
            $table->decimal('bank1', 8, 2)->nullable()->change();
            $table->decimal('bank2', 8, 2)->nullable()->change();
        });
    }
};
