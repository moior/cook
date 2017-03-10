<?php namespace Kenny\Memo\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use RainLab\User\Facades\Auth;
use Kenny\Memo\Models\Memo;

class Memos extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Memo anywhere',
            'description' => 'SK memo'
        ];
    }

    public function defineProperties()
    {
        return [
            'type' => [
                'description'       => 'Anywhere Memo',
                'title'             => 'Memo',
                'default'           => 'Final',
                'type'              => 'string',
                'validationPattern' => '^[a-z]+$',
                'validationMessage' => 'Please submit correct Memo type.'
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
        $this->page['memos'] = Memo::where( 'attach_type',  $this->property('attach_type'))
                        ->where( 'attach_id',  $this->property('attach_id'))
                        ->orderBy('id', 'desc')->get();

    }
    public function onNewMemo()
    {

        $form = post();
        $user = Auth::getUser(); //$user["email"]. $user->name; //
        $admin = BackendAuth::getUser();

        if( $form["attach_type"] && $form["attach_id"]){
            $memo = new Memo;
            if( isset($admin)){
                $memo->admin_id     = $admin->id; //first_name . ' ' .$admin->last_name;
                $memo->name         = $admin->first_name . ' ' .$admin->last_name;

            }
            $memo->attach_type    = isset($form["attach_type"])?$form["attach_type"]:"";
            $memo->attach_id      = isset($form["attach_id"])?$form["attach_id"]:"";
            $memo->content        = isset($form["content"])?$form["content"]:"";

            $memo->save();
        }
    }

}
