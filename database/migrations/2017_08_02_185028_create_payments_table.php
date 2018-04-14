<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->integer('project_payment_id')->unsigned(); //收款ID
            $table->integer('user_id')->unsigned(); //用户ID
            $table->tinyInteger('type'); //收款类型：1/招标，2/咨询
            $table->text('detail')->nullable(); //收款明细，json格式：{收款明目：收款金额}
            $table->decimal('subtotal', 7, 2)->nullable();  //收款小计
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
        Schema::drop('payments');
    }
}
