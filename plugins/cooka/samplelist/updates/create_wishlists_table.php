<?php namespace Cooka\Order\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateWishlistsTable extends Migration
{
    public function up()
    {
        Schema::create('cooka_wishlists', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->autoincrese();
            $table->integer('user_id');
            $table->string('cate');
            $table->string('title');
            $table->string('comment');

            $table->text('cook_data'); /*조합한 결과를 yaml 양식으로 저장 */
            $table->integer('ord');
            /*$table->integer('star');*/

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cooka_order_wishlists');
    }
}
