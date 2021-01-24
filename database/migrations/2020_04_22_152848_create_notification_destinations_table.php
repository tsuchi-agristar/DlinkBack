<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationDestinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('NotificationDestinations', function (Blueprint $table) {
            $table->char('notification_id', 36);
            $table->char('organization_id', 36);
            $table->boolean('confirm_status')->default(false)->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['notification_id', 'organization_id']);

            $table->foreign('notification_id')
                ->references('notification_id')
                ->on('Notifications');
            $table->foreign('organization_id')
                ->references('organization_id')
                ->on('Organizations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('NotificationDestinations');
    }
}
