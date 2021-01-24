<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Organizations', function (Blueprint $table) {
            $table->char('organization_id', 36)->unique();
            $table->integer('organization_type');
            $table->string('organization_name', 256)->nullable(true);
            $table->string('organization_name_kana', 256)->nullable(true);
            $table->string('prefecture', 32)->nullable(true);
            $table->string('city', 32)->nullable(true);
            $table->string('address', 128)->nullable(true);
            $table->string('homepage', 256)->nullable(true);
            $table->boolean('dummy')->default(false)->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->primary('organization_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Organizations');
    }
}
