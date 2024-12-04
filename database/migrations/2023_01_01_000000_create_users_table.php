<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('account_id')->default('0');
            $table->boolean('is_super_admin')->default(false);
            $table->tinyInteger('user_type')->default('2');
            $table->string('name')->nullable();
            $table->string('email',50)->unique()->nullable();
            $table->string('phone',50)->nullable();//unique()->
            $table->string('phone_prefix',50)->nullable();//unique()->
            $table->string('password')->nullable();
            $table->tinyInteger('url_status')->default('0');
            $table->string('public_url')->nullable();
            $table->tinyInteger('rating')->default('0');
            $table->tinyInteger('profile_status')->default('0');
            $table->tinyInteger('status')->default('1');
            $table->tinyInteger('verified')->default('0');
            $table->tinyInteger('phone_no_verified')->default('0');
            $table->tinyInteger('email_verified')->default('0');
            $table->tinyInteger('admin_approved')->default('0');
            $table->string('fcm_token')->nullable();
            $table->string('verified_token')->nullable();
            $table->string('email_verification_key',50)->nullable();
            $table->string('phone_verification_key',50)->nullable();
            $table->string('reset_pass_token',50)->nullable();
            $table->string('unique_code',50)->nullable();
            $table->string('auth_key',50)->nullable();
            $table->string('access_token',50)->nullable();
            $table->string('ip_address',50)->nullable();
            $table->string('activation_code')->nullable();
            $table->text('about')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('photo')->default('https://www.zaphri.com/uploads/defaults/user.png');
            $table->timestamp('last_seen')->nullable();
            $table->integer('gender')->nullable();
            $table->integer('privacy')->default(1);
            $table->string('language')->default('en');
            $table->tinyInteger('is_online')->default(0)->nullable();
            $table->tinyInteger('is_active')->default(0)->nullable();
            $table->tinyInteger('is_system')->default(0)->nullable();
            $table->tinyInteger('online_status')->default('0');
            $table->string('player_id')->unique()->nullable()->comment('One signal user id');
            $table->boolean('is_subscribed')->nullable();
            $table->integer('status_date')->default('0')->nullable();
            $table->integer('login_date')->default('0')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->rememberToken();

            $table->unsignedBigInteger('parent_id')->default('0');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['name']);
            $table->index(['email']);
            $table->index(['phone']);
        });

        /*Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email',50)->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('password_resets');
        Schema::dropIfExists('users');
    }
}
