<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Schools', function (Blueprint $table) {
            $table->char('school_id', 36)->unique();
            $table->integer('school_type')->nullable(true);
            $table->integer('student_number')->nullable(true);
            $table->boolean('scholarship_request')->nullable(true);
            $table->boolean('internship_request')->nullable(true);
            $table->boolean('practice_request')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->primary('school_id');
            $table->foreign('school_id')
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
        Schema::dropIfExists('Schools');
    }
}
