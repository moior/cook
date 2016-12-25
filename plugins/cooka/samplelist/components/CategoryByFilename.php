<?php namespace Cooka\Samplelist\Components;

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
        $imgpath = array();
        $file_path = '/plugins/cooka/samplelist/assets/images/note/';

        $catelist = ''; // 위 디렉토리 내에 -99.jpg를 뺀 나머지 앞부분 파일명으로 리스트 자동만듬.
    }
}
