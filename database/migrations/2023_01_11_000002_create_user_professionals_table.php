<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfessionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_professionals', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('club')->nullable();
            $table->tinyInteger('club_authentication')->default('0');
            $table->string('organizational_name',80)->nullable();
            $table->string('role',50)->nullable();
            $table->string('functional_area',150)->nullable();
            $table->text('responsablities')->nullable();
            $table->string('agegroups',50)->nullable();
            $table->string('gender',50)->nullable();
            $table->string('no_of_experience',150)->nullable();
            $table->string('experience',150)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('reference',80)->nullable();
            $table->string('exp_letter',80)->nullable();
            $table->tinyInteger('ispresent')->default('0');
            $table->tinyInteger('isdeleted')->default('0');

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
        Schema::dropIfExists('user_professionals');
    }
}
