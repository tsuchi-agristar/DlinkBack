<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Payments', function (Blueprint $table) {
            $table->char('payment_id', 36)->unique();
            $table->char('payment_hospital_id', 36);
            $table->dateTime('payment_month')->nullable(true);
            $table->integer('payment_status')->nullable(true);
            $table->integer('payment_price')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->primary('payment_id');
            $table->foreign('payment_hospital_id')
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
        Schema::dropIfExists('Payments');
    }
}
