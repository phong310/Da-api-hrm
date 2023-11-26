<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColLunchMWorkingDay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_working_day', function (Blueprint $table) {
            $table->time('start_lunch_break')->nullable();
            $table->time('end_lunch_break')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_working_day', function (Blueprint $table) {
            $table->dropColumn('start_lunch_break');
            $table->dropColumn('end_lunch_break');
        });
    }
}
