<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('generals', function (Blueprint $table) {
            $table->bigIncrements('id');
			
            $table->integer('reload_time')->default('3');
            $table->string('support_email')->nullable();
            $table->string('site_title')->nullable();
            $table->string('site_url')->nullable();
			
            $table->tinyInteger('paypal')->default('0');
            $table->string('paypal_account')->nullable();
            $table->string('paypal_client_id')->nullable();
            $table->string('paypal_secret_key')->nullable();
			
            $table->text('verify_subject')->nullable();
            $table->text('verify_email')->nullable();
			
            $table->text('verification_subject')->nullable();
            $table->text('verification_email')->nullable();
			
            $table->text('welcome_subject')->nullable();
            $table->text('welcome_email')->nullable();
			
            $table->text('forgot_subject')->nullable();
            $table->text('forgot_email')->nullable();
			
            $table->text('reset_subject')->nullable();
            $table->text('reset_email')->nullable();
			
            $table->text('request_subject')->nullable();
            $table->text('request_email')->nullable();
			
            $table->text('booking_subject')->nullable();
            $table->text('booking_email')->nullable();
			
            $table->text('cancel_subject')->nullable();
            $table->text('cancel_email')->nullable();
			
            $table->text('reschedule_subject')->nullable();
            $table->text('reschedule_email')->nullable();
			
            $table->text('inquire_event_subject')->nullable();
            $table->text('inquire_event_email')->nullable();
			
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            //$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('generals');
    }
}
