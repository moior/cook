<?php namespace Cooka\Samplelist\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Cook\Classes\FeeCalculator;
use RainLab\User\Facades\Auth;

class Samplelist extends ComponentBase
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


        $notename = $this->property('cate'); //"알림장";
        $dirpath = $this->property('dir')?:"/plugins/cooka/samplelist/assets/images/note"; // 해당 디렉토리 내 모든 파일 나열
        chdir($_SERVER['DOCUMENT_ROOT'].$dirpath);

        $imgdata = array();
        $glob =glob("$notename*");
        shuffle($glob);
        foreach ($glob as $key => $filename) {
            //echo "<br/>$filename " . "\n"; //filesize($filename)
            $temp["src"] = $dirpath."/".$filename;
            $temp["name"] = substr($filename, 0, strrpos($filename, "."));
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
