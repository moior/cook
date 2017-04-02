<?php namespace Boonzero\Sms\Components;

use Cms\Classes\ComponentBase;
use ApplicationException;
use Illuminate\Support\Facades\Session;
use Kenny\Sms\Model\SmsHistory;
use Backend\Facades\BackendAuth;
use RainLab\User\Facades\Auth;
use Kenny\Memo\Models\Memo;

class SmsControl extends ComponentBase
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
    /*전송하실 메세지를 배열에 담아, 한번 호출로 메세지를 일괄전송하실 수 있습니다.

    1)  반환되는 값은 0(실패) 또는 1(전송중)입니다.
    2) LMS(긴문자) 와 MMS(포토문자,이미지jpg파일지원) 발송시 제목(subject) 항목은 필수입니다.
    LMS 와 MMS 발송시 꼭 제목(subject) 을 입력해 주셔야 하며, 제목미입력시 "제목없음" 으로 표시되어 문자가 전송됩니다.
    3) MMS 의 이미지파일은, 800KB 미만으로 1개만 첨부가능하며, 확장자는 jpg / jpeg 파일로 전송하셔야 성공률이 높습니다.*/

    public function onRun()
    {
        $this->user = Auth::getUser();
        $this->admin = BackendAuth::getUser();
        if(empty($this->user)) {
            dd('로그인해야합니다.');
        }

        if( input('phone') == "[빈값]" ){
            $this->page['input_phone'] = '';
        }else{
            $this->page['input_phone'] = input('phone');
        }
    }
    public function onSendSms()
    {
        $sms = post();
        if( !$sms['발신번호'] )     $sms['발신번호'] = "010-4775-0852";
        if( !$sms['수신번호'] )     $sms['수신번호'] = "010-4775-0852"; //, 010-4618-7725
        if( !$sms['제목'] )         $sms['제목'] = '';
        if( !$sms['발신내용'] )     $sms['발신내용'] = "APIBOX에 오신것을 환영합니다.222";

        if( !$sms['문자타입'] )  {$type = "SMS"; $data['제목'] = null; }
        else { $type = $sms['문자타입']; $data['제목'] = "노트요리사"; }

        $ret = ''; //test시
        //$ret = $this->sendSms($sms);

        // SmsHistoryControl::onAddSmsHistory($sms); static이 아니므로 인스턴스 생성 후.
        (new SmsHistoryControl)->onAddSmsHistory($sms); /*전송내역 저장*/

        if($ret) Session::flash("alert", ['type'=>'success', 'message'=>"발송성공"]);
        else Session::flash("alert", ['type'=>'fail', 'message'=>"발송실패..."]);
    }
    private function sendSms($data){
        $level = error_reporting(0); // Notice 레벨 허용
        $LMS = false; $MMS = false;
        $수신번호들 = explode(",", $data['수신번호']);
        $수신번호들 = array_filter(array_map('trim',$수신번호들)); //화이트스페이스 제거 후에 빈것 제거

        $smsdata = array();
        foreach($수신번호들 as $수신번호){
            $tmp = array();
            if($data['제목']) $tmp[subject] = $data['제목']; //LMS, MMS 의 경우 필수
            $tmp[from] = $data['발신번호'];
            $tmp[to] = $수신번호;
            $tmp[message] = $data['발신내용'];
            $smsdata[] = $tmp;
        }
        include_once $_SERVER["DOCUMENT_ROOT"]."/plugins/boonzero/sms/components/apibox/library.php";
        $return = $apibox->sms_multi($smsdata); //붉은글씨 오류아님! $apibox->sms($data);
        error_reporting($level);
        return $return;
    }

    public function onNewMemo() /*onRun은 이것 이후에 실행되네..*/
    {
        $form = post();
        $this->user = Auth::getUser(); //$user["email"]. $user->name; //
        $this->admin = BackendAuth::getUser();

        if(!$this->user && !$this->admin) {
            // 로그인해야
        }else{
            if($form["memo_id"]){
                $this->updateMemo($form);
            }else{
                $this->insertMemo($form);
            }
        }
    }



}

