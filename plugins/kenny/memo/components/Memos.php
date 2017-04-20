<?php namespace Kenny\Memo\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Illuminate\Support\Facades\Session;
use RainLab\User\Facades\Auth;
use Kenny\Memo\Models\Memo;

class Memos extends ComponentBase
{
    private $user;
    private $admin;

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

        /*공통처리*/
        if ($this->property('attach_type')) {
            $this->page['attach_type'] = $this->property('attach_type');
        }
        if ($this->property('attach_id')) {
            $this->page['attach_id'] = $this->property('attach_id');
        }

        if( $this->admin ){ /*전부조회*/
            $this->page['memos'] = Memo::where( 'attach_type',  $this->property('attach_type'))
                ->where( 'attach_id',  $this->property('attach_id'))
                ->orderBy('id', 'asc')->get();
        }else if($this->user){ /*자기것만 조회*/
            $this->page['memos'] = Memo::where( 'attach_type',  $this->property('attach_type'))
                ->where( 'attach_id',  $this->property('attach_id'))
                ->where( 'user_id',  $this->user->id )
                ->orderBy('id', 'asc')->get();
        }else{
            Session::flash("action_result", "메모를 보시려면 로그인을 해주세요.");
            $this->page['memos'] = [
                [
                    "content"=>"로그인을 해주세요.",
                    "created_at"    => date("Y-m-d"),
                ],
            ];
        }


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
    private function insertMemo($form)
    {
        /*insert*/
        if ($form["attach_type"] && $form["attach_id"]) {
            $memo = new Memo;
            if ($this->admin) {
                $memo->admin_id = $this->admin->id;
                $memo->name = $this->admin->first_name . ' ' . $this->admin->last_name;
                /*만약.. 의뢰인한테 메모 남기려면, (상담이 의뢰인걸로 먼저 지정되어야 함)
                $memo->user_id = $order->user_id*/
            } else if ($this->user) {
                $memo->user_id = $this->user->id;
                $memo->name = $this->user->name;
            }
            $memo->attach_type = isset($form["attach_type"]) ? $form["attach_type"] : "";
            $memo->attach_id = isset($form["attach_id"]) ? $form["attach_id"] : "";
            $memo->content = isset($form["content"]) ? $form["content"] : "";
            $memo->level = isset($form["level"]) ? $form["level"] : "";

            date_default_timezone_set('Asia/Seoul'); //시간설정오류시. 서버 Default timezone = UTC이네..

            $memo->save();
            Session::flash("action_result", "새 메모가 저장되었습니다.");
        } else {
            Session::flash("action_result", "연결모델 입력 오류 23");
        }
    }

    private function updateMemo($form)/*그렇게 필요하진 않음*/
    {
        $memo = Memo::find( $form["memo_id"] );
        if($memo) { /* 기존 있으면 수정 */
            /*update*/
            if ($form["attach_type"] && $form["attach_id"]) {
                if ($this->admin && $memo->admin_id == $this->admin->id) {
                    $memo->content = isset($form["content"]) ? $form["content"] : "";
                    $memo->level = isset($form["level"]) ? $form["level"] : "";

                    date_default_timezone_set('Asia/Seoul'); //시간설정오류시. 서버 Default timezone = UTC이네..

                    $memo->save();
                    Session::flash("action_result", "수정되었습니다.");
                }else{
                    Session::flash("action_result", "관리자만(작성자) 수정 가능합니다.");
                }
            }else{
                Session::flash("action_result", "연결모델 수정 오류 13");
            }
        }else{
            Session::flash("action_result", "연결모델 수정 오류 12");
        }
    }

}

