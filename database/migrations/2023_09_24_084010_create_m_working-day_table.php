<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMWorkingDayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_working_day', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->unsignedTinyInteger('type');
            $table->time('start_time');
            $table->time('end_time');
            $table->bigInteger('day_in_week_id')->unsigned();
            $table->foreign('day_in_week_id')
                ->references('id')
                ->on('days_in_week');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_working_day');
    }
}
