<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('NotificationBlocks', function (Blueprint $table) {
            $table->char('organization_id', 36)->unique();
            $table->timestamps();

            $table->primary('organization_id');

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
        Schema::dropIfExists('NotificationBlocks');
    }
}
