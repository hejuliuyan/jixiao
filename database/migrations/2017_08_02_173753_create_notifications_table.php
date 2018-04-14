<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned(); //项目ID
            $table->integer('from_user_id')->unsigned(); //提示发起者ID
            $table->tinyInteger('from_user_result'); //提示发起者的操作结果
            $table->integer('user_id')->unsigned(); //用户ID
            $table->text('body')->nullable(); //提示内容
            $table->tinyInteger('status')->default(0); //是否已读：0/否，1/是
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
        Schema::drop('notifications');
    }
}
