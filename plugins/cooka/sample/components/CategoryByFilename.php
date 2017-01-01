<?php namespace Cooka\Sample\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Cook\Classes\FeeCalculator;
use RainLab\User\Facades\Auth;

class CategoryByFilename extends ComponentBase
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

        /*파라미터로 디렉토리 넘기도록*/
        $dirpath = "/plugins/cooka/sample/assets/images/note";
        if ($handle = opendir(".".$dirpath)) {         // glob가 더 편하다 함. 나중. foreach (glob("/") as $filename) { echo "<br/><br/>$filename size " . filesize($filename) . "\n"; }
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $filelist[] = $entry;
                    //echo "aaaaaaaaa"."$entry\n";
                }
            }
            closedir($handle);
        }
        asort($filelist);
        /*알림장-02.jpg 에서 -02.jpg 를 빼고, 중복 제거해서 디렉토리 리스트 만듬*/
        foreach( $filelist as $key => $file){ //"알림장-수학-02.jpg";
            //$catename_all[] = basename($file, strrchr($file, '-')); // 뒤에서 처음 나오는 "-"까지 // 이상함. 될때있고 안될떄 있고... 나중 이상생기면 여기가 문제.
            $catename_all[]  = substr($file, 0, strrpos($file, "-"));

        }
        $catename = array_unique($catename_all);
        $this->page['catelist2'] = $catename;


    }
}
