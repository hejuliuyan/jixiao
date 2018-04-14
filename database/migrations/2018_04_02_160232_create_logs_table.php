<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->increments('logs_id');
            $table->integer('payment_id')->default(0)->comment('pid_increments');
            $table->decimal('money')->default(0)->comment('进账金额');
            $table->string('invoice_num')->default(0)->comment('发票编号');
            $table->string('type')->comment('类型 income：收入咨询；flat_pay：工本费；assess_income：专家评审；other_pay：其他支出；assess_pay：评审支出');
            $table->text('remarks')->comment('备注');
            $table->integer('create_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('logs');
    }
}
