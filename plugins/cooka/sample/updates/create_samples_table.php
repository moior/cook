<?php namespace Cooka\Sample\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateSamplesTable extends Migration
{
    public function up()
    {
        Schema::create('cooka_samples', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->autoincrese();
            $table->string('cate'); /* 슬래시(/)로 구분. 종이/노트 */
            $table->string('menu'); /* 업무노트. 식으로 메뉴구성. group by 로 뽑아낼것임. */
            $table->string('title');
            $table->text('spec'); /*사이즈 재질 등 객관 스펙*/
            $table->text('comment'); /*우리가 쓰는 코멘트. 만드는데 힘들었다.. 등*/
            /*$table->string('like'); /**/

            $table->int('stock'); /*재고수량*/
            $table->text('cook_data'); /*혹 제작한것을 자동 샘플 업로드할떄. 나중. 조합한 결과 저장 */
            $table->text('bill'); /*당시 견적서*/
            $table->string('status_show');
            $table->boolean('is_hidden');

            $table->timestamps();
        });
    }
    /*라이크 테이블
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `userid` int(11) NOT NULL,
    `post_type` varchar(10) NOT NULL,
    `post_id` int(11) NOT NULL,
    `date` datetime NOT NULL,
     PRIMARY KEY (`id`)*/
    public function down()
    {
        Schema::dropIfExists('cooka_samples');
    }
}
