<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalFairTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('HospitalFairTypes', function (Blueprint $table) {
            $table->char('append_information_id', 36);
            $table->integer('hospital_fair_type');

            $table->foreign('append_information_id')
                ->references('append_information_id')
                ->on('HospitalFairs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('HospitalFairTypes');
    }
}
