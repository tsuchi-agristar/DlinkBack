<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventMembersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('EventMembers', function (Blueprint $table) {
            $table->char('event_id', 36);
            $table->char('organization_id', 36);
            $table->integer('member_role')->nullable(true);

            $table->primary(['event_id', 'organization_id']);
            $table->foreign('event_id')
                ->references('event_id')
                ->on('OnlineEvents');
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
        Schema::dropIfExists('EventMembers');
    }
}
