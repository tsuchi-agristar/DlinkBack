<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Notifications', function (Blueprint $table) {
            $table->char('notification_id', 36);
            $table->integer('notification_type')->nullable(true);
            $table->dateTime('notification_at')->nullable(true);
            $table->text('title')->nullable(true);
            $table->text('content_school')->nullable(true);
            $table->text('content_hospital')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('Notifications');
    }
}
