<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_details', function (Blueprint $table) {
            $table->bigIncrements('id');
			
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->double('amount')->default(0); 
            $table->double('paid_amount')->default(0); 
            $table->unsignedInteger('pay_date')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->unsignedInteger('ref_req_date')->default(0);
            $table->unsignedInteger('ref_date')->default(0);
			
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
			
			$table->foreign('payment_id')->references('id')->on('payments')
            ->onDelete('cascade');
			$table->foreign('booking_id')->references('id')->on('bookings')
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
        Schema::dropIfExists('payment_details');
    }
}
