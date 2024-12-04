<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('club_id')->nullable();
            $table->unsignedBigInteger('coach_id')->nullable();
            $table->unsignedBigInteger('ast_coach_id')->nullable();
            $table->string('name',50)->nullable();           
            $table->tinyInteger('age_group')->default(0);
            $table->string('description')->nullable();
            $table->string('color',50)->nullable();
            $table->string('logo')->nullable();
            $table->tinyInteger('status')->default(1);
			
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
			
			$table->foreign('club_id')->references('id')->on('users')
            ->onDelete('cascade');
			$table->foreign('coach_id')->references('id')->on('users')
            ->onDelete('cascade');
			$table->foreign('ast_coach_id')->references('id')->on('users')
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
        Schema::dropIfExists('teams');
    }
}
