<?php namespace Kenny\Memo\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('cooka_invoices', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->autoincrese();
            $table->integer('user_id');
            $table->integer('order_id');

            $table->text('cooka_data');
            $table->text('bill');
            $table->text('content');
            $table->integer('fee');

            $table->string('staff_id'); /* 견적서 발송인 */
            $table->string('status');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cooka_invoices');
    }
}
