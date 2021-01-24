<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFairDetailsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('FairDetails', function (Blueprint $table) {
            $table->char('fair_id', 36);
            $table->char('append_information_id', 36);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('fair_id')
                ->references('fair_id')
                ->on('Fairs');
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
        Schema::dropIfExists('FairDetails');
    }
}
