<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('NotificationQueues', function (Blueprint $table) {
            $table->char('notification_id', 36);
            $table->integer('notification_type')->nullable(true);
            $table->char('operation_id', 36)->nullable(true);
            $table->dateTime('notification_at')->nullable(true);

            $table->primary('notification_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('NotificationQueues');
    }
}
