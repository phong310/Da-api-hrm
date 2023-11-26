<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdentificationCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('identification_cards', function (Blueprint $table) {
            $table->id();
            $table->string('ID_no', 50);
            $table->date('issued_date');
            $table->string('issued_by');
            $table->string('ID_expire');
            $table->unsignedBigInteger('personal_information_id');
            $table->tinyInteger('type');
            $table->timestamps();

            $table->foreign('personal_information_id')->references('id')->on('personal_information')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('identification_cards');
    }
}
