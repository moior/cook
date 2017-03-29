<?php namespace Boonzero\Sms\Components;

use Boonzero\Sms\Models\SmsContent;
use Cms\Classes\ComponentBase;
use ApplicationException;
use RainLab\User\Facades\Auth;
use Backend\Facades\BackendAuth;

class SmsContentControl extends ComponentBase
{
    private $user;
    private $admin;

    public function componentDetails()
    {
        return [
            'name'        => 'Sms Content',
            'description' => 'Sms Content List.'
        ];
    }

    public function defineProperties()
    {
        return [
            'type' => [
                'description'       => 'Sms Content',
                'title'             => 'Sms Content',
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

        $sms_db = SmsContent::where("user_id", $this->user->id)
            ->orWhere("admin_id", $this->admin->id)
            ->orderBy("category")->orderBy("ord")->get();
        $this->page['sms_contents'] = $sms_db;

        $form = post();
    }

    public function onListSmsContent()
    {

    }
    public function onAddSmsContent()
    {
        $this->user = Auth::getUser();
        $this->admin = BackendAuth::getUser();

        $form = post();
        $sms = new SmsContent();

        $sms->user_id = $this->user->id;  // 주소록 소유자
        $sms->admin_id = $this->admin->id; // 주소록 소유자 (어드민)

        $sms->category = isset($form["category"])?$form["category"]:"";
        $sms->title = isset($form["title"])?$form["title"]:"";
        $sms->content = isset($form["content"])?$form["content"]:"";
        $sms->level = isset($form["level"])?$form["level"]:"";
        $sms->ord = (isset($form["ord"]) && is_numeric($form["ord"]) )?$form["ord"]:0;
        $sms->save();
    }
    public function onModSmsContent()
    {
        $form = post();
        $sms = SmsContent::find( $form["id"] );
        $sms->category = isset($form["category"])?$form["category"]:"";
        $sms->title = isset($form["title"])?$form["title"]:"";
        $sms->content = isset($form["content"])?$form["content"]:"";
        $sms->level = isset($form["level"])?$form["level"]:"";
        $sms->ord = (isset($form["ord"]) && is_numeric($form["ord"]) )?$form["ord"]:0;
        $sms->save();
    }
    public function onDelSmsContent()
    {
        $form = post();
        SmsContent::destroy( $form["id"] );
    }
}
