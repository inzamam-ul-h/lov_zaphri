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
        Schema::create('conversations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('from_id')->nullable();
            $table->unsignedInteger('to_id')->nullable();
            $table->text('message');
            $table->tinyInteger('status')->default(0)->comment('0 for unread,1 for seen');
            $table->tinyInteger('message_type')->default(0)->comment('0- text message, 1- image, 2- pdf, 3- doc, 4- voice');
            $table->text('file_name')->nullable();
            $table->timestamps();
            /*$table->foreign('from_id')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('to_id')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');*/
        });
		
        /*Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign('conversations_to_id_foreign');
            $table->dropIndex('conversations_to_id_foreign');
        });*/

        Schema::table('conversations', function (Blueprint $table) {
            $table->string('to_id')->change();
            $table->string('to_type')->default(\App\Models\Conversation::class)->after('to_id')->comment('1 => Message, 2 => Group Message');
        });
		
        Schema::table('conversations', function (Blueprint $table) {
            $table->unsignedInteger('reply_to')->nullable();

            $table->foreign('reply_to')->references('id')->on('conversations')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
		
        Schema::table('conversations', function (Blueprint $table) {
            $table->text('url_details')->nullable();
        });
		
        Schema::table('conversations', function (Blueprint $table) {
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversations');
    }
};
