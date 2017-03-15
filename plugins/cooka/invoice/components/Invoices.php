<?php namespace Cooka\Invoice\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Cooka\Invoice\Models\Invoice;
use Mail;
use RainLab\User\Facades\Auth;
use Kenny\Memo\Models\Memo;

class Invoices extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Invoice anywhere',
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

        if ($this->property('order_id')) {
            $this->page['invoices'] = Invoice::where( 'order_id',  $this->property('order_id'))
                ->get();
            //$this->page['order_id'] = $this->property('order_id');
        }else{
            $this->page['invoices'] = Invoice::paginate(10);
//            $this->page['invoices'] = array();
        }

    }

    public function onShowAllList()
    {

    }

    public function onShowRelatedList()
    {
        $this->page['invoices'] = Invoices::where( 'order_id',  $this->property('order_id'))
            ->get();
    }

    public function onSendInvoice()
    {

        $form = post();
        $user = Auth::getUser(); //$user["email"]. $user->name; //
        $auth = BackendAuth::getUser();
        if( $form["content"]){
            $invoice = new Invoice;

            $invoice->user_id    = isset($form["user_id"])?$form["user_id"]:0;
            $invoice->order_id    = isset($form["order_id"])?$form["order_id"]:"";
            $invoice->cook_data      = isset($form["cook_data"])?$form["cook_data"]:"";
            $invoice->bill        = isset($form["bill"])?$form["bill"]:"";
            $invoice->content       = isset($form["content"])?$form["content"]:"";
            if( isset($auth->id)){
                $invoice->staff_id     = $auth->id;
            }else{
                $invoice->staff_id     = "0";
            }
            $invoice->fee        = isset($form["fee"])?$form["fee"]:0;
            $invoice->status        = isset($form["status"])?$form["status"]:"";

            $invoice->save();

            /*이메일 보내기 // 로컬서버에서는 안됨.*/
            if(!$form["name"]) $customer_name = $form["name"];
            else $customer_name = "고객";

            $data = [
                'name' => $customer_name,
                'content' => $form["content"],
                'bill' => $invoice->bill,
            ];
            $emaillist = $form["email"] . ",help@moior.com";
            $emails = explode(',', $emaillist);
            foreach( $emails as $key => $email ) {
                $email = trim($email);
                Mail::send('cooka.invoice::mail.sendinvoice', $data, function($message) use ($customer_name, $email) {
                    $message->to($email, $customer_name);
                });
            }

            /*SMS 보내기*/
            $phones = explode(',', $form["phone"]);
            foreach( $phones as $key => $phone ) {
                $ret = \Kenny\Sms\Components\SmsController::send([
                            //======================80자======================================================??
                    'text'=>'노트요리사::견적서가 이메일로 발송되었습니다. - 직접 만드는 나만의 노트, 노트요리사.com',
                    'from' => '02-1661-5521',
                    'to' => $phone
                ]);
            }

            /*메모에 추가하기*/
            $memo = new Memo;
            $memo->attach_type = "Order";
            $memo->attach_id = $invoice->order_id;
            $memo->content = "견적서 발송 (총액: ".number_format(ceil($invoice->fee))."원) ";
            $memo->save();
        }
    }

}
