<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flows', function (Blueprint $table) {
            $table->integer('project_flow_id')->unsigned(); //流转ID
            $table->integer('user_id')->unsigned()->nullable();; //用户ID
            $table->tinyInteger('result'); //操作结果
            $table->text('remark')->nullable(); //备注
            $table->timestamps();

            //创建主键
            $table->primary(['project_flow_id', 'result']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('flows');
    }
}
