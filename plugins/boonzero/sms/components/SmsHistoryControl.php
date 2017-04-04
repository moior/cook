<?php namespace Boonzero\Sms\Components;

use Boonzero\Sms\Models\SmsHistory;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Backend\Facades\BackendAuth;
use RainLab\User\Facades\Auth;

class SmsHistoryControl extends ComponentBase
{
    private $user;
    private $admin;

    public function componentDetails()
    {
        return [
            'name'        => 'History',
            'description' => 'Sms History List.'
        ];
    }

    public function defineProperties()
    {
        return [
            'type' => [
                'description'       => 'History',
                'title'             => 'History',
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

        $sms_db = SmsHistory::where("user_id", $this->user->id)
            ->orWhere("admin_id", $this->admin->id)
            ->orderBy("id", "desc")->paginate(10);

        $this->page['sms_historys'] = $sms_db;
    }

    public function onListSmsHistory()
    {
        $this->user = Auth::getUser();
        $this->admin = BackendAuth::getUser();

        $form = post();
        $sms = SmsHistory::where("user_id", $this->user)
            ->orWhere("admin_id", $this->admin)
            ->orderBy("id")->get();
        return $sms;
    }
    public function onAddSmsHistory($data, $ret = null)
    {
        $this->user = Auth::getUser();
        $this->admin = BackendAuth::getUser();

        $form = $data; //post();
        $sms = new SmsHistory();

        $sms->user_id = $this->user->id;  // 주소록 소유자
        $sms->admin_id = $this->admin->id; // 주소록 소유자 (어드민)

        $form['phone_from'] = $form['발신번호'] ;
        $form['phone_to'] = $form['수신번호'] ;
        $form['title'] = $form['제목'] ;
        $form['message'] = $form['발신내용'] ;
        $form['sms_type'] = $form['문자타입'];
        $form["result"] = ($ret)?"성공":"실패";

        $sms->receiver_id = 0; // 회원끼리 주고받을때..?
        $sms->attach_model =    isset($form["attach_model"])?$form["attach_model"]:"";
        $sms->attach_id =       isset($form["attach_id"])?$form["attach_id"]:0;

        $sms->phone_from =      isset($form["phone_from"])?$form["phone_from"]:"";
        $sms->phone_to =        isset($form["phone_to"])?$form["phone_to"]:"";
        $sms->title =           isset($form["title"])?$form["title"]:"";
        $sms->message =         isset($form["message"])?$form["message"]:"";
        $sms->result =          isset($form["result"])?$form["result"]:"";
        $sms->sms_type =        isset($form["sms_type"])?$form["sms_type"]:"";
        $sms->save();
    }
    public function onModSmsHistory()
    {
        $form = post();
        $sms = SmsHistory::find( $form["id"] );

        $sms->user_id = $form["user_id"];
        $sms->category = $form["category"];
        $sms->attach_model = $form["attach_model"];
        $sms->attach_id = $form["attach_id"];
        $sms->phone_from = $form["phone_from"];
        $sms->phone_to = $form["phone_to"];
        $sms->title = $form["title"];
        $sms->message = $form["message"];
        $sms->result = $form["result"];
        $sms->save();
    }
    public function onDelSmsHistory()
    {
        $form = post();
        SmsHistory::destroy( $form["id"] );
    }
}
