<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationManagesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('NotificationManages', function (Blueprint $table) {
        //     $table->char('notification_manage_id', 36)->unique();
        //     $table->char('organization_id', 36);
        //     $table->char('notification_id', 36);
        //     $table->boolean('confirm_status')->nullable(true);
        //     $table->timestamps();
        //     $table->softDeletes();

        //     $table->primary('notification_manage_id');
        //     $table->foreign('organization_id')
        //         ->references('organization_id')
        //         ->on('Organizations');
        //     $table->foreign('notification_id')
        //         ->references('notification_id')
        //         ->on('Notifications');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('NotificationManages');
    }
}
