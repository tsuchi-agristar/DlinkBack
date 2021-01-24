<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Users', function (Blueprint $table) {
            $table->char('user_id', 36)->unique();
            $table->char('organization_id', 36);
            $table->string('mail_address', 128);
            $table->string('account_name', 128)->unique()->nullable();
            $table->string('password', 128)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary('user_id');
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
        Schema::dropIfExists('Users');
    }
}
