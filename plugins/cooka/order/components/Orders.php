<?php namespace Cooka\Order\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Cook\Classes\FeeCalculator;
use Cooka\Order\Models\Order;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
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

        /*if(isset($user->id)){
            $orders = Order::where('user_id', $user->id)->orderBy("created_at", "desc")->get(); //find( $user->id );
        }else{
            $orders = Order::orderBy("created_at", "desc")->paginate(20); //find( $user->id );
        }*/
        $search_text = $this->property('search');

        $orders = Order::orderBy("created_at", "desc"); //find( $user->id );
        if($search_text){
            $orders = $orders->where("name", "like", "%". $search_text."%");
            $orders = $orders->orWhere("phone", "like", "%". $search_text."%");
            $orders = $orders->orWhere("email", "like", "%". $search_text."%");
            $orders = $orders->orWhere("addr", "like", "%". $search_text."%");
            $orders = $orders->orWhere("cook_data", "like", "%". $search_text."%");
            $orders = $orders->orWhere("status_show", "like", "%". $search_text."%");
        }
        $orders = $orders->paginate(10);

        $this->page['orders'] = $orders;

        $upload_file_arr = array();
        foreach( $orders as $key => $order){
            if($order->upload_file)
                $upload_file_arr[] = $order->upload_file->getPath();
        }
        $this->page['upload_file_arr'] = $upload_file_arr;
        /*print_r( $upload_file_arr);*/
        foreach( $orders as $key => $order){
            $cook_data_arr[$key] = json_decode($order['cook_data'], true); //true:배열, false:객체
        }
        if( isset($cook_data_arr)){
            $this->page['cook_data'] = $cook_data_arr;
        }



    }

    /*접수버튼 클릭시*/
    public function onSaveOrder()
    {

        $form = post();
        $order = $this->saveOrder($form);

        $this->sendMailAfterOrder($order, "cooka.order::mail.ordered");
        $this->sendSMSAfterOrder($order,
            '노트요리사::주문완료되었습니다. 확인 후 연락 드리겠습니다. 노트요리사.com');
            /*======================80자======================================================?? */

        /*우리 요리사들에게 문자*/
        $ret = \Kenny\Sms\Components\SmsController::send([
            'text'=>'노트요리사::주문접수됨! '. $order->name. '/' . $order->phone. '/'
                .$order->title. '/'. $order->fee_offer,
            'from' => '02-1661-5521',
            'to' => '010-4775-0852, 010-4618-7725',
        ]); /*김상겸, 장미라에게 문자*/

    }


    /*전화받아서 상담입력시*/
    public function onSaveOrderAdmin()
    {
        $form = post();
        $order = $this->saveOrder($form);

        /*의뢰인께 내용 알릴려면*/
        if(isset($form["send_email"]) && $form["send_email"] == true){
            $this->sendMailAfterOrder($order, "cooka.order::mail.thanksForContact");
        }
        if(isset($form["send_sms"]) && $form["send_sms"] == true) {
            if( !isset($form["send_sms_text"])){
                $form["send_sms_text"] = "[직접 만드는 나만의 노트, 노트요리사.com] 상담이 접수되었습니다 감사합니다.";
            }
            $this->sendSMSAfterOrder($order, $form["send_sms_text"]);
        }
    }

    /*DB에 저장 내부함수*/
    private function saveOrder($form){
        $order = new Order;
        $order->name        = isset($form['client_name'])?$form['client_name']:"";

        //$user = BackendAuth::getUser(); //$user["email"]. $user->name; //
        $user = Auth::getUser(); //$user["email"]. $user->name; //
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
        $order->sample_code     = isset($form["sample_code"])?$form["sample_code"]." ".$form["sample_name"]:"";

        $order->cook_data       = isset($form["cook_data"])?$form["cook_data"]:""; // yml 으로 변환 //$parsed = yaml_parse($yaml);        // convert the YAML back into a PHP variable
        if(Input::hasFile('upload_file')){//Request::hasFile(
            $order->upload_file     = Input::file('upload_file'); //$form["upload_file"]; //
        }
        $order->bill            = isset($form["bill"])?$form["bill"]:"";
        $order->fee_offer       = isset($form["fee_offer"])?$form["fee_offer"]:"0";
        //$order->fee_payment     = $form[""];
        $order->status_show     = isset($form["status_show"])?$form["status_show"]:"접수확인중";
        $order->shipping_number = "";

        $order->save();
        return $order;
    }

    private function sendMailAfterOrder($order, $mail_view = "cooka.order::mail.ordered"){
        if( $order->email ){
            $data = [
                'bill' => $order->bill,
                'name' => $order->name,
            ];
            Mail::send($mail_view, $data, function($message) use ($order) {
                $message->to($order->email, $order->name);
            });
        }
    }
    private function sendSMSAfterOrder($order, $sms_text = "[노트요리사] 상담이 접수되었습니다. 노트요리사.com")
    {
        if ($order->phone) {
            $ret = \Kenny\Sms\Components\SmsController::send([
                /*======================80자======================================================?? */
                'text' => $sms_text,
                'from' => '02-1661-5521',
                'to' => $order->phone
            ]);
        }
    }
}
//$items = post('items', []); // 원래있는것. 배열처리방법.
/*if (!count($items)) {
    throw new ApplicationException(sprintf('뭔가 이상 : %s ', $this->property('type')));
}*/
//$user = BackendAuth::getUser(); //$user["email"]. $user->name; //
