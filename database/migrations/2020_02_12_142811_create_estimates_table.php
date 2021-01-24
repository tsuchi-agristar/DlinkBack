<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateEstimatesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Estimates', function (Blueprint $table) {
            $table->char('estimate_id', 36);
            $table->char('event_id', 36)->nullable(true);
            $table->integer('estimate_status')->nullable(true);
            $table->integer('regular_price')->nullable(true);
            $table->integer('discount_price')->nullable(true);
            $table->integer('estimate_price')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->primary('estimate_id');
            $table->foreign('event_id')
            ->references('event_id')
            ->on('OnlineEvents');

        });
        if (DB::getDriverName() === 'sqlsrv') {
            DB::statement('CREATE UNIQUE INDEX estimates_event_id_unique'
               . ' ON Estimates(event_id)'
               . ' WHERE event_id IS NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Estimates');
    }
}
