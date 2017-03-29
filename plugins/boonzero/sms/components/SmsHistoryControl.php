<?php namespace Boonzero\Sms\Components;

use Cms\Classes\ComponentBase;
use ApplicationException;

class SmsHistoryControl extends ComponentBase
{

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

    }

    public function onListSmsHistory()
    {
        $form = post();
        $sms = SmsHistory::where("category", "노트제작")->orderBy("ord")->get();
        return $sms;
    }
    public function onAddSmsHistory()
    {
        $form = post();
        $sms = new SmsHistory;
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
