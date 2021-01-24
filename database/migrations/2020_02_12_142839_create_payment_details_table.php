<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentDetailsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('PaymentDetails', function (Blueprint $table) {
            $table->char('payment_id', 36);
            $table->char('estimate_id', 36);
            
            $table->foreign('payment_id')
                ->references('payment_id')
                ->on('Payments');
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
        Schema::dropIfExists('PaymentDetails');
    }
}
