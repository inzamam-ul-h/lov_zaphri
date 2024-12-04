<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPersonalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_personals', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->string('first_name',50)->nullable();
            $table->string('last_name',50)->nullable();
            $table->date('dob')->nullable();
            $table->string('gender',50)->default('Male');
            $table->string('contact_number',50)->nullable();
            $table->unsignedBigInteger('country')->nullable();
            $table->unsignedBigInteger('state')->nullable();
            $table->unsignedBigInteger('city')->nullable();
            $table->string('street',50)->nullable();
            $table->string('house',50)->nullable();
            $table->string('pin_location',50)->nullable();
            $table->string('zip_code',50)->nullable();
            $table->string('about_me')->nullable();
            $table->string('meetinglink',80)->nullable();
            $table->string('coachpic')->nullable();
            $table->string('reg_no',50)->nullable();
            $table->string('contact_person',50)->nullable();
            $table->string('address')->nullable();
            $table->tinyInteger('status')->default('1');
            $table->integer('modified')->nullable();

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
        Schema::dropIfExists('user_personals');
    }
}
