<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->unSignedBigInteger('province_id');
            $table->unSignedBigInteger('district_id');
            $table->unSignedBigInteger('ward_id');
            $table->string('address', 100);
            $table->tinyInteger('type');
            $table->unSignedBigInteger('personal_information_id');
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
        Schema::dropIfExists('addresses');
    }
}
