<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->bigIncrements('id');
			
            $table->unsignedBigInteger('user_id');
            $table->string('title')->nullable();
            $table->string('print_title')->nullable();
            $table->string('duration',80)->nullable();
            $table->unsignedBigInteger('category');
            $table->text('description')->nullable();
            $table->text('print_description')->nullable();
            $table->string('image')->nullable();
            $table->string('print_image')->nullable();
            $table->string('video')->nullable();
            $table->unsignedBigInteger('author');
            $table->unsignedBigInteger('recipients');
            $table->tinyInteger('status')->default(1);
			
            $table->timestamp('date_of_creation');
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
        Schema::dropIfExists('videos');
    }
}
