<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnlineEventsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('OnlineEvents', function (Blueprint $table) {
            $table->char('event_id', 36)->unique();
            $table->char('fair_id', 36)->nullable(true);
            $table->integer('event_type')->nullable(true);
            $table->integer('event_status')->default(config('const.EVENT_STATUS')['UNDECIDED']);
            $table->integer('channel_status')->default(config('const.CHANNEL_STATUS')['CLOSE']);
            $table->dateTime('start_at')->nullable(true);
            $table->dateTime('end_at')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->primary('event_id');
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
        Schema::dropIfExists('OnlineEvents');
    }
}
