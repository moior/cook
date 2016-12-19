<?php namespace Cooka\Order\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('cooka_order_orders', function(Blueprint $table) {
            /*$table->engine = 'InnoDB';*/
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cooka_order_orders');
    }
}
