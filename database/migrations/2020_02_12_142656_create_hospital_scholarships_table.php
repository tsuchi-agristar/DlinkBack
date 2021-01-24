<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalScholarshipsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('HospitalScholarships', function (Blueprint $table) {
            $table->char('append_information_id', 36)->unique();
            $table->text('target_person')->nullable(true);
            $table->text('document_submitted')->nullable(true);
            $table->text('selection_system')->nullable(true);
            $table->text('loan_amount')->nullable(true);
            $table->date('loan_period_start')->nullable(true);
            $table->date('loan_period_end')->nullable(true);
            $table->date('payback_period_start')->nullable(true);
            $table->date('payback_period_end')->nullable(true);
            $table->text('payback')->nullable(true);
            $table->boolean('payback_exemption')->nullable(true);
            $table->text('payback_exemption_condition')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->primary('append_information_id');
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
        Schema::dropIfExists('HospitalScholarships');
    }
}
