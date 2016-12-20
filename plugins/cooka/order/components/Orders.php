<?php namespace Cooka\Order\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Cook\Classes\FeeCalculator;
use Cooka\Order\Models\Order;

class Orders extends ComponentBase
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

        $user = BackendAuth::getUser(); //$user["email"]. $user->name; //

        if(isset($user->id)){
            $orders = Order::where('user_id', $user->id)->orderBy("id", "desc")->get(); //find( $user->id );
        }else{
            $orders = Order::pagination(20); //find( $user->id );
        }
        $this->page['orders'] = $orders;
        //return "<pre>".$this->page['orders'];
        foreach( $orders as $key => $order){
            $cook_data_arr[] = json_decode($order['cook_data'], true);
        }
        $this->page['specs'] = $cook_data_arr;
        /*print_r($cook_data_arr);*/
    }
}
