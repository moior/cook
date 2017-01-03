<?php namespace Cooka\Sample\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Cook\Classes\FeeCalculator;
use Cooka\Sample\Models\Sample;
use RainLab\User\Facades\Auth;

class SampleList extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Order list',
            'description' => ''
        ];
    }

    public function defineProperties()
    {
        return [
            'type' => [
                'description'       => 'choose column to display at order list',
                'title'             => 'Choose column',
                'default'           => '',
                'type'              => 'string',
                'validationPattern' => '^[1-9a-z]+$',
                'validationMessage' => 'Please submit correct column type.'
            ]
        ];
    }



    /* This code will be executed when the page or layout is
    // loaded and the component is attached to it.*/
    public function onRun()
    {

        $user = Auth::getUser(); //$user["email"]. $user->name; //
        $imgdata = array();

        /*DB에 있는것 부름. 나중 위에 파일기반은 삭제해야함.*/
        if($this->property('menu')){
            $samples = Sample::where("menu", $this->property('menu'))->orderByRaw("RAND()"); //limit(15)->

        }else{
            $samples = Sample::orderByRaw("RAND()"); //limit(15)->
        }
        if($this->property('count')) {
            $samples = $samples->limit($this->property('count'));
        }
        $samples = $samples->get();

        foreach ($samples as $key => $sample) {
            /*$temp["src"] = $dirpath."/../"."더보기.png";
            $temp["name"] = "더보기";
            $imgdata[] = $temp;*/
            /*attatchMany 복수이므로*/
            foreach ($sample->sample_images as $sample_image) {
                $temp["src"] = $sample_image->getPath();
                $temp["thumb"] = $sample_image->getThumb(140, 160, ['mode'=>'crop']);
                $temp["name"] = $sample->title;//"첨부";//$sample_image->getName();
                $temp["sample_id"] = $sample->id;//$sample_image->getName();
                $imgdata[] = $temp;
            }
            //getThumb(100, 100, ['mode' => 'crop']);
        }




        $notename = $this->property('menu'); //"알림장";
        $dirpath = $this->property('dir')?:"/plugins/cooka/sample/assets/images/note"; // 해당 디렉토리 내 모든 파일 나열
        chdir($_SERVER['DOCUMENT_ROOT'].$dirpath);

        $glob =glob("$notename*");
        shuffle($glob);
        foreach ($glob as $key => $filename) {
            //echo "<br/>$filename " . "\n"; //filesize($filename)
            $temp["src"] = $dirpath."/".$filename;
            $temp["thumb"] = "";
            $temp["name"] = substr($filename, 0, strrpos($filename, "."));
            $temp["sample_id"] = 0;
            $imgdata[] = $temp;
            if($key > 6) {
                $temp["src"] = $dirpath."/../"."더보기.png";
                $temp["name"] = "더보기";
                $imgdata[] = $temp; break;
            }
        }



        $this->page['imgdata'] = $imgdata;

    }


}
