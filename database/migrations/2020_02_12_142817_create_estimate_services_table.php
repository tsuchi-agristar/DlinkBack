<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstimateServicesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('EstimateServices', function (Blueprint $table) {
            $table->char('estimate_id', 36);
            $table->integer('hospital_type')->nullable(true);
            
            $table->foreign('estimate_id')
                ->references('estimate_id')
                ->on('Estimates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('EstimateServices');
    }
}
