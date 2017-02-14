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
        // This code will be executed when the page or layout is
        // loaded and the component is attached to it.
        if ($this->property('attach_type')) {
            $this->page['attach_type'] = $this->property('attach_type');
        }
        if ($this->property('attach_id')) {
            $this->page['attach_id'] = $this->property('attach_id');
        }
        $this->page['invoices'] = Memo::where( 'attach_type',  $this->property('attach_type'))
                        ->where( 'attach_id',  $this->property('attach_id'))->get();

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

            /*이메일 보내기*/
            $data = [
                'name' => $form["name"],
                'content' => $form["content"],
                'bill' => $invoice->bill,
            ];
            $emails = explode(',', $form["email"]);
            foreach( $emails as $key => $email ) {
                Mail::send('cooka.invoice::mail.sendinvoice', $data, function($message) use ($form, $email) {
                    $message->to($email, $form["name"]);
                });
            }

            /*SMS 보내기*/
            $phones = explode(',', $form["phone"]);
            foreach( $phones as $key => $phone ) {
                $ret = \Kenny\Sms\Components\SmsController::send([
                    /*======================80자======================================================?? */
                    'text'=>'노트요리사::견적서가 이메일로 발송되었습니다. (총금액:'.$invoice->fee.'원) 노트요리사.com',
                    'from' => '02-1661-5521',
                    'to' => $phone
                ]);
            }

            /*메모에 추가하기*/
            $memo = new Memo;
            $memo->attach_type = "Order";
            $memo->attach_id = $invoice->order_id;
            $memo->content = "견적서 발송함. 총액: ".$invoice->fee."원. ".date("Y-m-d H:i");
            $memo->save();
        }
    }

}
