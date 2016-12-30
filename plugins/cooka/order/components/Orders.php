<?php namespace Cooka\Order\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Cook\Classes\FeeCalculator;
use Cooka\Order\Models\Order;
use Illuminate\Support\Facades\Input;
use RainLab\User\Facades\Auth;

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

        $user = Auth::getUser(); //$user["email"]. $user->name; //

        if(isset($user->id)){
            $orders = Order::where('user_id', $user->id)->orderBy("created_at", "desc")->get(); //find( $user->id );
        }else{
            $orders = Order::orderBy("created_at", "desc")->paginate(20); //find( $user->id );
        }
        $this->page['orders'] = $orders;

        foreach( $orders as $key => $order){
            if($order->upload_file)
                $upload_file_arr[] = $order->upload_file->getPath();
            else  $upload_file_arr[] = '';
        }
        $this->page['upload_file_arr'] = $upload_file_arr;
        /*print_r( $upload_file_arr);*/
        foreach( $orders as $key => $order){
            $cook_data_arr[] = json_decode($order['cook_data'], true);
        }
        if( isset($cook_data_arr)){
            $this->page['specs'] = $cook_data_arr;
        }

    }

    /*접수버튼 클릭시*/
    public function onSaveOrder()
    {
        //$items = post('items', []); // 원래있는것. 배열처리방법.
        /*if (!count($items)) {
            throw new ApplicationException(sprintf('뭔가 이상 : %s ', $this->property('type')));
        }*/
        //$user = BackendAuth::getUser(); //$user["email"]. $user->name; //
        $user = Auth::getUser(); //$user["email"]. $user->name; //

        /*만약 로긴 아니면, guest 모델 만들어서 가입할까......*/
        /*$user = $this->user;*/
        $order = new Order; /*Db::table('users')->insert(
                                ['email' => 'john@example.com', 'votes' => 0]
                                );*/
        $form = post();
        $order->name        = isset($form['client_name'])?$form['client_name']:"";
        if( isset($user->id)){
            $order->user_id     = $user->id;
        }else{
            $order->user_id     = "0";
        }
        $order->title           = isset($form["title"])?$form["title"]:"";
        $order->phone           = isset($form["client_tel"])?$form["client_tel"]:"";
        $order->addr            = isset($form["client_addr"])?$form["client_addr"]:"";
        $order->email           = isset($form["client_email"])?$form["client_email"]:"";
        $order->comment         = isset($form["client_comment"])?$form["client_comment"]:"";
        $order->sample_code     = isset($form["sample_code"])?$form["sample_code"]:"";

        //$order->wishlist_id = form[""];
        $order->cook_data       = isset($form["cook_data"])?$form["cook_data"]:""; // yml 으로 변환 //$parsed = yaml_parse($yaml);        // convert the YAML back into a PHP variable
        if(Input::hasFile('upload_file')){//Request::hasFile(
            $order->upload_file     = Input::file('upload_file'); //$form["upload_file"]; //
        }
        $order->bill            = isset($form["bill"])?$form["bill"]:"";
        $order->fee_offer       = isset($form["fee_offer"])?$form["fee_offer"]:"0";
        //$order->fee_payment     = $form[""];
        $order->status_show     = isset($form["status_show"])?$form["status_show"]:"입금확인";
        $order->shipping_number = "";
        //$order->is_complete     = "";

        return $order->save();
    }
}
