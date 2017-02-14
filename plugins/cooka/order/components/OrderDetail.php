<?php namespace Cooka\Order\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Cook\Classes\FeeCalculator;
use Cooka\Order\Models\Order;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use RainLab\User\Facades\Auth;

class OrderDetail extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Notecooker',
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
        $orderId = $this->property('id');
        $order = Order::find( $orderId );
        $this->page['order'] = $order;

        if(isset($order->cook_data)) {
            $this->page['cook_data'] = json_decode($order->cook_data, true);
        }else $this->page['cook_data'] = null;

            /*일반 로그인*/
        $user = Auth::getUser(); //$user["email"]. $user->name; //
        /*관리자 로그인*/
        $admin = BackendAuth::getUser();
        if ($admin) { // && $admin->hasAccess('cooka.sample.create')
            $this->page['admin'] = true;
        }else { $this->page['admin'] = false; }
    }


}
