<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_details', function (Blueprint $table) {
            $table->bigIncrements('id');
			
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('about_zaphry')->nullable();
            $table->string('phone',50)->nullable();
            $table->string('email',50)->nullable();
            $table->string('address')->nullable();
            $table->string('whatsapp',50)->nullable();
            $table->string('facebook',80)->nullable();
            $table->string('twitter',80)->nullable();
            $table->string('dribble',80)->nullable();
            $table->string('linkdin',80)->nullable();
            $table->string('youtube',80)->nullable();
			
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
			
			$table->foreign('user_id')->references('id')->on('users')
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
        Schema::dropIfExists('contact_details');
    }
}
