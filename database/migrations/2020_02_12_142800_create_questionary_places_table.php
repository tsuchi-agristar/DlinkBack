<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionaryPlacesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('QuestionaryPlaces', function (Blueprint $table) {
            $table->char('questionary_id', 36);
            $table->string('place', 128)->nullable(true);
            
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
        Schema::dropIfExists('QuestionaryPlaces');
    }
}
