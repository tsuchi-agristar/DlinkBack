<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionariesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Questionary', function (Blueprint $table) {
            $table->char('questionary_id', 36)->unique();
            $table->char('school_id', 36);
            $table->dateTime('answered_datetime')->nullable(true);
            $table->dateTime('desire_start_at')->nullable(true);
            $table->dateTime('desire_end_at')->nullable(true);
            $table->text('comment')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->primary('questionary_id');
            $table->foreign('school_id')
                ->references('school_id')
                ->on('Schools');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Questionary');
    }
}
