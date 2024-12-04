<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
			
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('type')->default(1);
            $table->string('name',50)->nullable();           
            $table->double('price')->default(0);
            $table->string('description')->nullable();
            $table->string('color',50)->nullable();
            $table->unsignedInteger('time_start')->nullable();
            $table->unsignedInteger('time_end')->nullable();
            $table->tinyInteger('req_booking')->default(0);
            $table->tinyInteger('inv_booking')->default(0);
            $table->tinyInteger('booked')->default(0);
            $table->tinyInteger('status')->default(1);
			
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
			
			$table->foreign('user_id')->references('id')->on('users')
            ->onDelete('cascade');
			$table->foreign('type')->references('id')->on('session_types')
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
        Schema::dropIfExists('sessions');
    }
}
