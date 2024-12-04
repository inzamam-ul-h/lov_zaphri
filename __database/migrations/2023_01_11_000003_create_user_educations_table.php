<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserEducationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_educations', function (Blueprint $table) {
            $table->bigIncrements('id');
			
            $table->unsignedBigInteger('user_id');
            $table->string('title',80)->nullable();
            $table->string('major_course',150)->nullable();
            $table->string('Intitute_name',150)->nullable();
            $table->unsignedBigInteger('country')->nullable();
            $table->unsignedBigInteger('state')->nullable();
            $table->unsignedBigInteger('city')->nullable();	
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('reference',80)->nullable();
            $table->string('certificates',80)->nullable();
            $table->tinyInteger('ispresent')->default('0');
            $table->tinyInteger('isdeleted')->default('0');
			
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
			
			$table->foreign('user_id')->references('id')->on('users')
            ->onDelete('cascade');
			$table->foreign('country')->references('id')->on('countries')
            ->onDelete('cascade');
			$table->foreign('state')->references('id')->on('states')
            ->onDelete('cascade');
			$table->foreign('city')->references('id')->on('cities')
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
        Schema::dropIfExists('user_educations');
    }
}
