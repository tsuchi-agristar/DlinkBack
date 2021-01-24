<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalAppendsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('HospitalAppends', function (Blueprint $table) {
            $table->char('append_information_id', 36)->unique();
            $table->integer('append_information_type')->nullable(true);
            $table->char('hospital_id', 36);
            $table->integer('recruiting_job_type')->nullable(true);
            $table->date('recruiting_period_start')->nullable(true);
            $table->date('recruiting_period_end')->nullable(true);
            $table->text('content')->nullable(true);
            $table->text('various_matters')->nullable(true);
            $table->text('other')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->primary('append_information_id');
            $table->foreign('hospital_id')
                ->references('hospital_id')
                ->on('Hospitals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('HospitalAppends');
    }
}
