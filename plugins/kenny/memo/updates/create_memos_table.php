<?php namespace Kenny\Memo\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateMemosTable extends Migration
{
    public function up()
    {
        Schema::create('cooka_memos', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->autoincrese();
            $table->integer('user_id');
            $table->string('attach_type');
            $table->integer('attach_id');

            $table->text('content');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cooka_memos');
    }
}
