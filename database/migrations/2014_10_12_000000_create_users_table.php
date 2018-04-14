<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->tinyInteger('division'); //部门类别
            $table->tinyInteger('is_super')->default(0); //是否为超级管理员：0/否，1/是
            $table->tinyInteger('is_banned')->default(0); //是否禁用：0/否，1/是
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
