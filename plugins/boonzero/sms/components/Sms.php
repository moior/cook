<?php namespace Boonzero\Sms\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Illuminate\Support\Facades\Session;
use RainLab\User\Facades\Auth;
use Kenny\Memo\Models\Memo;

class Sms extends ComponentBase
{
    private $user;
    private $admin;

    public function componentDetails()
    {
        return [
            'name'        => 'Sms anywhere',
            'description' => 'SK Sms'
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

    public function onInit()
    {
        echo "";
    }

    public function onStart()
    {
        echo "";
    }

    public function onRun()
    {
        $this->user = Auth::getUser();
        $this->admin = BackendAuth::getUser();

        /*require_once "./apibox/library.php";*/
        require_once $_SERVER["DOCUMENT_ROOT"]."/plugins/boonzero/sms/components/apibox/library.php";
        $data['from'] = "02-1234-5678";
        $data['to'] = "010-4775-0852";
        $data['message'] = "APIBOX에 오신것을 환영합니다.";

        //$return = $apibox->sms($data);

    }
    public function onNewMemo() /*onRun은 이것 이후에 실행되네..*/
    {
        $form = post();
        $this->user = Auth::getUser(); //$user["email"]. $user->name; //
        $this->admin = BackendAuth::getUser();

        if(!$this->user && !$this->admin) {
            // 로그인해야
            Session::flash("action_result", "메모를 남기시려면 로그인을 해주세요.");
        }else{
            if($form["memo_id"]){
                $this->updateMemo($form);
            }else{
                $this->insertMemo($form);
            }
        }
    }



}

