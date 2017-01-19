<?php namespace Cooka\Sample\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Cook\Classes\FeeCalculator;
use Cooka\Sample\Models\Sample;
use RainLab\User\Facades\Auth;

class SampleMenu extends ComponentBase
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
        /*$user = Auth::getUser(); //$user["email"]. $user->name; //

        $cate = "종이/노트";
        $catename = array();
        $sampleMenus = Sample::select("menu")->where("cate", $cate)->groupBy("menu")->get();
        //$sampleMenus = DB::table("cooka_samples")->groupBy("menu")->get();
        foreach ($sampleMenus as $key => $menu) {
                $catename[] = $menu->menu;
        }
        $this->page['menulist'] = $catename;*/


    }
}
