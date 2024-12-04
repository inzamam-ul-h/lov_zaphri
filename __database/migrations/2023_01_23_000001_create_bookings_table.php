<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
			
            $table->unsignedBigInteger('user_id');           
            $table->unsignedBigInteger('req_user_id');
            $table->unsignedBigInteger('session_id');
            $table->tinyInteger('status')->default(1);
            $table->string('coach_cancellation_reason')->nullable();
            $table->string('player_cancellation_reason')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->string('req_security',50)->nullable();
            $table->string('inv_security',50)->nullable();
            $table->tinyInteger('coach_feedback')->default(0);
            $table->tinyInteger('coach_delivery')->default(0);
            $table->tinyInteger('coach_rating')->default(0);
            $table->string('coach_remarks')->nullable();
            $table->tinyInteger('player_feedback')->default(0);
            $table->tinyInteger('player_delivery')->default(0);
            $table->tinyInteger('player_rating')->default(0);
            $table->string('player_remarks')->nullable();
            $table->tinyInteger('is_file_uploaded')->default(0);
            $table->string('file_path',80)->nullable();
            $table->tinyInteger('is_assessed')->default(0);
            $table->tinyInteger('obt_marks')->default(0);
            $table->string('remarks')->nullable();
			
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
			
			$table->foreign('user_id')->references('id')->on('users')
            ->onDelete('cascade');
			$table->foreign('req_user_id')->references('id')->on('users')
            ->onDelete('cascade');
			$table->foreign('session_id')->references('id')->on('sessions')
            ->onDelete('cascade');
			$table->foreign('payment_id')->references('id')->on('payments')
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
        Schema::dropIfExists('bookings');
    }
}
