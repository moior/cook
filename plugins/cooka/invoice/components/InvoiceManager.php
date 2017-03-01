<?php namespace Cooka\Invoice\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Cooka\Invoice\Models\Invoice;
use Mail;
use RainLab\User\Facades\Auth;
use Kenny\Memo\Models\Memo;

class InvoiceManager extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'InvoiceManager',
            'description' => 'SK Invoice'
        ];
    }

    public function defineProperties()
    {
        return [
            'type' => [
                'description'       => 'Anywhere Invoice',
                'title'             => 'Invoice',
                'default'           => 'Final',
                'type'              => 'string',
                'validationPattern' => '^[a-z]+$',
                'validationMessage' => 'Please submit correct Invoice type.'
            ]
        ];
    }

    public function onRun()
    {


    }


}
