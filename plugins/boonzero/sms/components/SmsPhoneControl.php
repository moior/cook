<?php namespace Boonzero\Sms\Components;

use Boonzero\Sms\Models\SmsPhone;
use Cms\Classes\ComponentBase;
use ApplicationException;
use RainLab\User\Facades\Auth;
use Backend\Facades\BackendAuth;

class SmsPhoneControl extends ComponentBase
{
    private $user;
    private $admin;

    public function componentDetails()
    {
        return [
            'name'        => 'Phone',
            'description' => 'List a Sms Phone List.'
        ];
    }

    public function defineProperties()
    {
        return [
            'type' => [
                'description'       => 'Phone',
                'title'             => 'Phone',
                'default'           => 'Final',
                'type'              => 'string',
                'validationPattern' => '^[a-z]+$',
                'validationMessage' => 'Please submit correct Bill type.'
            ]
        ];
    }


    /**/
    public function onRun()
    {
        $this->user = Auth::getUser();
        $this->admin = BackendAuth::getUser();

        $sms_db = SmsPhone::where("user_id", $this->user->id)
            ->orWhere("admin_id", $this->admin->id)
            ->orderBy("category")->orderBy("ord")->get();
        if(empty($sms_db)){
            // 최초 자료 입력해줌
        }else{
            $this->page['sms_phones'] = $sms_db;
        }
    }

    public function onListSmsPhone()
    {

    }
    public function onAddSmsPhone() /*onRun 보다 먼저 실행됨*/
    {
        $this->user = Auth::getUser();
        $this->admin = BackendAuth::getUser();

        $form = post();
        $sms = new SmsPhone;

        $sms->user_id = $this->user->id;  // 주소록 소유자
        $sms->admin_id = $this->admin->id; // 주소록 소유자 (어드민)

        $sms->category = isset($form["category"])?$form["category"]:"";
        $sms->name = isset($form["name"])?$form["name"]:"";
        $sms->phone = isset($form["phone"])?$form["phone"]:"";
        $sms->comment = isset($form["comment"])?$form["comment"]:"";
        $sms->level = isset($form["level"])?$form["level"]:"";
        $sms->ord =  (isset($form["ord"]) && is_numeric($form["ord"]) )?$form["ord"]:0;
        $sms->save();
    }
    public function onModSmsPhone()
    {
        $this->user = Auth::getUser();
        $this->admin = BackendAuth::getUser();

        $form = post();
        $sms = SmsPhone::find( $form["id"] );
        //$sms->user_id = $this->user->id;  // 주소록 소유자
        //$sms->admin_id = $this->admin->id;  // 주소록 소유자

        $sms->category = isset($form["category"])?$form["category"]:"";
        $sms->name = isset($form["name"])?$form["name"]:"";
        $sms->phone = isset($form["phone"])?$form["phone"]:"";
        $sms->comment = isset($form["comment"])?$form["comment"]:"";
        $sms->level = isset($form["level"])?$form["level"]:"";
        $sms->ord = isset($form["ord"])?$form["ord"]:0;
        $sms->save();
    }
    public function onDelSmsPhone()
    {
        $form = post();
        SmsPhone::destroy( $form["id"] );
    }
}
