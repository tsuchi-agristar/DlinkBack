<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFairApplicationsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('FairApplications', function (Blueprint $table) {
            $table->char('application_id', 36)->unique();
            $table->char('fair_id', 36);
            $table->char('school_id', 36);
            $table->dateTime('application_datetime')->useCurrent();
            $table->integer('application_status')->default(config('const.APPLICATION_STATUS')['APPLYING']);
            $table->integer('estimate_participant_number')->nullable(true);
            $table->integer('format')->nullable(true);
            $table->text('comment')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->primary('application_id');
            $table->foreign('fair_id')
                ->references('fair_id')
                ->on('Fairs');
            $table->foreign('school_id')
                ->references('school_id')
                ->on('Schools');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('FairApplications');
    }
}
