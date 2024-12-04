<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_calendars', function (Blueprint $table) {
            $table->bigIncrements('id');
			
            $table->unsignedBigInteger('user_id');  
            $table->unsignedBigInteger('time_zone')->default('1');
            $table->string('date_format')->default('MM/DD/YYYY');
            $table->string('time_format')->default('12h (am/pm)');
            $table->string('available_from')->nullable();
            $table->string('available_to')->nullable();
            $table->tinyInteger('calendar_view')->default('1');
            $table->tinyInteger('sunday_sts')->default('0');
            $table->tinyInteger('monday_sts')->default('1');
            $table->tinyInteger('tuesday_sts')->default('1');
            $table->tinyInteger('wednesday_sts')->default('1');
            $table->tinyInteger('thursday_sts')->default('1');
            $table->tinyInteger('friday_sts')->default('1');
            $table->tinyInteger('saturday_sts')->default('0');
			
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
			
			$table->foreign('user_id')->references('id')->on('users')
            ->onDelete('cascade');
			$table->foreign('time_zone')->references('id')->on('time_zones')
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
        Schema::dropIfExists('user_calendars');
    }
}
