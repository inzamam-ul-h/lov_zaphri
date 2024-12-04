<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingProgramDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_program_details', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('program_id');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->tinyInteger('duration')->default(1);
            $table->string('start_date_time',50)->nullable();
            $table->text('images')->nullable();
            $table->text('original_documents_name')->nullable();
            $table->text('documents')->nullable();
            $table->text('videos')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

			$table->foreign('program_id')->references('id')->on('training_programs')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('training_program_details');
    }
}
