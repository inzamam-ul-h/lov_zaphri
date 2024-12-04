<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingPlanDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_plan_details', function (Blueprint $table) {
            $table->bigIncrements('id');
			
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('video_id');
            $table->tinyInteger('status')->default(1);
			
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
			
			$table->foreign('plan_id')->references('id')->on('training_plans')
            ->onDelete('cascade');
			$table->foreign('video_id')->references('id')->on('videos')
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
        Schema::dropIfExists('training_plan_details');
    }
}
