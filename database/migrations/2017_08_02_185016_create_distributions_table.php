<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributions', function (Blueprint $table) {
            $table->integer('project_distribution_id')->unsigned(); //分配ID
            $table->tinyInteger('position'); //项目分工编号
            $table->integer('user_id')->unsigned(); //用户ID
            $table->text('remark')->nullable(); //备注
            $table->integer('bonus')->nullable(); //奖金金额
            $table->string('formula')->nullable(); //计算公式
            $table->timestamps();

            //创建主键
            $table->primary(['project_distribution_id', 'position', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('distributions');
    }
}
