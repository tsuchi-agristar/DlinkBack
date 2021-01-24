<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionaryHospitalTypesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('QuestionaryHospitalTypes', function (Blueprint $table) {
            $table->char('questionary_id', 36);
            $table->integer('hospital_type')->nullable(true);
            
            $table->foreign('questionary_id')
                ->references('questionary_id')
                ->on('Questionary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('QuestionaryHospitalTypes');
    }
}
