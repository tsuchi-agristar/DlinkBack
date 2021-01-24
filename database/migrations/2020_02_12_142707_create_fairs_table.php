<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFairsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Fairs', function (Blueprint $table) {
            $table->char('fair_id', 36)->unique();
            $table->char('hospital_id', 36);
            $table->integer('fair_status')->nullable(true);
            $table->dateTime('plan_start_at')->nullable(true);
            $table->dateTime('plan_end_at')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->primary('fair_id');
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
        Schema::dropIfExists('Fairs');
    }
}
