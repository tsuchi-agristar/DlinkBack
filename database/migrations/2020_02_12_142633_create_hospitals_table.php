<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Hospitals', function (Blueprint $table) {
            $table->char('hospital_id', 36);
            $table->integer('hospital_type')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->primary('hospital_id');
            $table->foreign('hospital_id')
                ->references('organization_id')
                ->on('Organizations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Hospitals');
    }
}
