<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone_number', 20);
            $table->string('tax_code');
            $table->string('address', 200);
            $table->tinyInteger('status')->default(0);
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->datetime('register_date');
            $table->string('representative', 150)->nullable();
            $table->tinyInteger('type_of_business')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
