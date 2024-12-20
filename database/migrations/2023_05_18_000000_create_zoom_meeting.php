<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoom_meetings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('topic');
            $table->dateTime('start_time');
            $table->string('duration');
            $table->boolean('host_video');
            $table->boolean('participant_video');
            $table->text('agenda');
            $table->unsignedInteger('created_by');
            $table->text('meta')->nullable();
            $table->timestamps();

            /*$table->foreign('created_by')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');*/
        });
		
        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->integer('status')->default(1);
            $table->string('meeting_id');
        });
		
        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->string('time_zone')->default(null);
            $table->string('password');
        });
		
        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->integer('duration')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zoom_meetings');
    }
};
