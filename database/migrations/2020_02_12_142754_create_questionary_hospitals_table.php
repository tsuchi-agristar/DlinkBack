<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionaryHospitalsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('QuestionaryHospitals', function (Blueprint $table) {
            $table->char('questionary_id', 36);
            $table->char('hospital_id', 36);
            
            $table->foreign('questionary_id')
                ->references('questionary_id')
                ->on('Questionary');
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
        Schema::dropIfExists('QuestionaryHospitals');
    }
}
