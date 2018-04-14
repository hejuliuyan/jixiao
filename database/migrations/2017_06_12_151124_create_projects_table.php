<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned(); //创建的用户ID
            $table->tinyInteger('division'); //所属部门
            $table->string('num'); //项目编号
            $table->string('name'); //项目名称
            $table->tinyInteger('type'); //项目类型：1/招标，2/咨询，3/混合类型
            $table->decimal('cost', 10, 4)->nullable();  //工程造价
            $table->string('contract_num', 50)->nullable(); //咨询合同编号
            $table->decimal('contract_price', 8, 4)->nullable();  //合同费用
            $table->string('depute_name'); //委托单位名称
            $table->string('depute_user', 50); //委托联系人
            $table->string('depute_phone', 20); //委托联系电话
            $table->string('category'); //项目类别，格式：1,2,3
            $table->string('category_text')->nullable(); //自定义项目类别
            $table->tinyInteger('single')->default(0); //单项工程：0/否，1/是
            $table->tinyInteger('unit')->default(0); //单位工程：0/否，1/是
            $table->tinyInteger('extract')->default(1); //不提取项目信息：0/否，1/是
            $table->tinyInteger('record')->default(0); //咨询合同备案：0/否，1/是
            $table->tinyInteger('state')->default(0); //当前操作状态
            $table->tinyInteger('status')->default(0); //是否申请奖金分配
            $table->timestamps();

            //创建编号索引
            $table->unique(['num']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('projects');
    }
}
