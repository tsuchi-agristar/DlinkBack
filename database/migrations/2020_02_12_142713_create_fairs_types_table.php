<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFairsTypesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('FairsTypes', function (Blueprint $table) {
            $table->char('fair_id', 36);
            $table->integer('fair_type');

            $table->foreign('fair_id')
                ->references('fair_id')
                ->on('Fairs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('FairsTypes');
    }
}
