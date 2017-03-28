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

        $form = post();
        $sms = SmsContent::where("category", "노트제작")->orderBy("ord")->get();
    }

    public function onListSmsContent()
    {

    }
    public function onAddSmsContent()
    {
        $form = post();
        $sms = new SmsContent();
        $sms->user_id = $form["user_id"];
        $sms->category = $form["category"];
        $sms->title = $form["title"];
        $sms->attach_model = $form["attach_model"];
        $sms->content = $form["content"];
        $sms->level = $form["level"];
        $sms->ord = $form["ord"];
        $sms->save();
    }
    public function onModSmsContent()
    {
        $form = post();
        $sms = SmsContent::find( $form["id"] );
        $sms->user_id = $form["user_id"];
        $sms->category = $form["category"];
        $sms->title = $form["title"];
        $sms->attach_model = $form["attach_model"];
        $sms->content = $form["content"];
        $sms->level = $form["level"];
        $sms->ord = $form["ord"];
        $sms->save();
    }
    public function onDelSmsContent()
    {
        $form = post();
        $sms = SmsContent::find( $form["id"] );
        $sms->delete();
    }
}
