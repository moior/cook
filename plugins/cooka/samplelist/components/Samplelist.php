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
        $imgpath = array();
        $notename = $this->property('cate'); //"알림장";

        for ($num =1; $num <  10; $num++){
            $numm = sprintf("%02d", $num);

            $file_path = '/plugins/cooka/samplelist/assets/images/note/'.$notename.'-'.$numm.'.jpg';
            if( file_exists($_SERVER['DOCUMENT_ROOT'].$file_path)){
                $imgpath[] = $file_path;
            }
        }
        $this->page['imgsrc'] = $imgpath;
        /*if(isset($user->id)){
            $orders = Order::where('user_id', $user->id)->orderBy("created_at", "desc")->get(); //find( $user->id );
        }else{
            $orders = Order::orderBy("created_at", "desc")->paginate(20); //find( $user->id );
        }
        $this->page['orders'] = $orders;
        //return "<pre>".$this->page['orders'];
        foreach( $orders as $key => $order){
            $cook_data_arr[] = json_decode($order['cook_data'], true);
        }
        if( isset($cook_data_arr)){
            $this->page['specs'] = $cook_data_arr;
        }*/
        /*print_r($cook_data_arr);*/
    }
}
