<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalPracticesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('HospitalPractices', function (Blueprint $table) {
            $table->char('append_information_id', 36)->unique();
            $table->text('target_person')->nullable(true);
            $table->date('practice_period_start')->nullable(true);
            $table->date('practice_period_end')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->primary('append_information_id');
            $table->foreign('append_information_id')
                ->references('append_information_id')
                ->on('HospitalAppends')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('HospitalPractices');
    }
}
