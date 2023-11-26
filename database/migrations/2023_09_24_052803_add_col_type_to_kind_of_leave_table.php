<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColTypeToKindOfLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kind_of_leave', function (Blueprint $table) {
            $table->unsignedTinyInteger('type')->default(1)->after('company_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kind_of_leave', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
