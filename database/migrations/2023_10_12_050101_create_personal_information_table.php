<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_information', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->unsignedBigInteger('job_id');
            $table->string('nickname', 100)->nullable();
            $table->date('birthday');
            $table->string('marital_status');
            $table->tinyInteger('sex');
            $table->string('email', 100);
            $table->string('phone', 20);
            $table->unsignedBigInteger('education_level_id')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('ethnic', 50)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('job_id')->references('id')->on('m_jobs');
            $table->foreign('education_level_id')->references('id')->on('m_education_level')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('m_countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personal_information');
    }
}
