<?php

use App\Models\Master\WorkingDay;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColTotalWorkingTimeMWorkingDayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_working_day', function (Blueprint $table) {
            $table->unsignedMediumInteger('total_working_time')->after('day_in_week_id');
        });

        $workingDays = WorkingDay::query()->get();

        foreach ($workingDays as $wd) {
            $totalTime = Carbon::parse($wd->start_time)->floatDiffInMinutes(Carbon::parse($wd->end_time));

            if ($wd->start_lunch_break && $wd->end_lunch_break) {
                $totalTime -= Carbon::parse($wd->start_lunch_break)->floatDiffInMinutes(Carbon::parse($wd->end_lunch_break));
            }

            if ($totalTime > 0) {
                $wd->total_working_time = $totalTime;
                $wd->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_working_day', function (Blueprint $table) {
            $table->dropColumn('total_working_time');
        });
    }
}
