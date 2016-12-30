<?php namespace Cooka\Order\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('cooka_orders', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->autoincrese();
            $table->integer('user_id');
            $table->string('title');
            $table->string('name');
            $table->string('phone');
            $table->string('addr');
            $table->string('email');
            $table->string('comment'); /*마지막에 입력하는 요청사항*/

            $table->integer('wishlist_id');
            $table->text('cook_data'); /*조합한 결과를 yaml 양식으로 저장 */
            $table->text('bill');
            $table->integer('fee_offer');
            $table->string('fee_payment'); /*입금내역을 글자로*/
            $table->string('status_show');
            $table->string('shipping_number');
            $table->boolean('is_complete');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cooka_orders');
    }
}
