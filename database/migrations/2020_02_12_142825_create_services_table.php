<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Services', function (Blueprint $table) {
            $table->char('service_id', 36);
            $table->integer('service_type')->nullable(true);
            $table->integer('fair_format')->nullable(true);
            $table->integer('school_number')->nullable(true);
            $table->integer('location')->nullable(true);
            $table->integer('price')->nullable(true);

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
        Schema::dropIfExists('Services');
    }
}
