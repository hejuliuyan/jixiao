<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_flows', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned(); //项目ID
            $table->tinyInteger('num')->default(1); //操作次数
            $table->tinyInteger('status')->default(0); //流转是否完成：0/否，1/是
            $table->timestamps();

            //创建索引
            $table->unique(['project_id', 'num']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('project_flows');
    }
}
