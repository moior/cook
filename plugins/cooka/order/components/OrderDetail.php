<?php namespace Cooka\Order\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Cook\Classes\FeeCalculator;
use Cooka\Order\Models\Order;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use RainLab\User\Facades\Auth;
use Renatio\DynamicPDF\Classes\PDF;


class OrderDetail extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Notecooker',
            'description' => ''
        ];
    }

    public function defineProperties()
    {
        return [
            'type' => [
                'description' => 'choose column to display at order list',
                'title' => 'Choose column',
                'default' => '',
                'type' => 'string',
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
        $order = Order::find($orderId);
        /*$imsiOrder = $order;
        print_r($order);
        foreach($order->attribute as $key => $order__){
            if( !$order__ ){
                $order->attribute->$key = "[값없음]";
                echo $key."----------@@ ";
            }
        }*/
        $tmpOrder = $order['attributes'];

        foreach( $tmpOrder as $key => $order_){
            if(empty($order_)){
                $tmpOrder[$key] = "[빈값]";
            }
        }
        $this->page['order'] = $tmpOrder;
        /*$this->page['order'] = $order;*/

        if($order->upload_file) {
            $this->page['upload_file'] = $order->upload_file->getPath();
        }

        if (isset($order->cook_data)) {
            $this->page['cook_data'] = json_decode($order->cook_data, true);
        } else $this->page['cook_data'] = null;

        /*일반 로그인*/
        $user = Auth::getUser(); //$user["email"]. $user->name; //
        /*관리자 로그인*/
        $admin = BackendAuth::getUser();
        if ($admin) { // && $admin->hasAccess('cooka.sample.create')
            $this->page['admin'] = true;
        } else {
            $this->page['admin'] = false;
        }
    }

    public function onUpdateStatus(){

        $orderId = post('order_id');
        $order = Order::find($orderId);
        $order->status_show = post('status_show');
        $order->save();
    }
    public function onDeleteOrder(){
        $orderId = post('order_id');
        $order = Order::find($orderId);
        $order->delete();
    }

    /*텍스트 클릭시 입력창이 뜨고 바로수정 가능케*/
    public function onUpdateColumnEasy(){
        $post = post();
        if( $post["order_id"] ){
            $orderId = $post["order_id"];
            $order = Order::find($orderId);
            /*$order->update(['name' => $post["column_value"]]);*/
            $column_name = $post["column_name"];
            $order->$column_name = $post["column_value"];
            $order->save();
        }
    }
    public function showPdf()
    {
        $templateCode = 'Invoice::note'; // unique code of the template
        $data = ['name' => 'Kenny']; // optional data used in template

        return PDF::loadTemplate($templateCode, $data)->stream();
    }

}
