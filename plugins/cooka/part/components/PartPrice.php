<?php namespace Cooka\Part\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Cook\Classes\FeeCalculator;
use Cooka\Part\Models\Part;
use Cooka\Sample\Models\Sample;
use RainLab\User\Facades\Auth;
use Cooka\Part\Components\PartControl;

class PartPrice extends ComponentBase
{
    private $user;
    private $admin;

    public function componentDetails()
    {
        return [
            'name'        => 'Part Price',
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
    }

}
