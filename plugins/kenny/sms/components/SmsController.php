<?php namespace Kenny\Sms\Components;

use Cms\Classes\ComponentBase;
use Kenny\Sms\Components\Coolsms;
/*use Illuminate\Support\Facades\Auth; octobercms 에서는 아래 */
use RainLab\User\Facades\Auth;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class SmsController extends ComponentBase {
    public function componentDetails()
    {
        return [
            'name'        => 'aa list',
            'description' => ''
        ];
    }
    /*
    |--------------------------------------------------------------------------
    | SMS Controller
    |--------------------------------------------------------------------------
    |
    | 문자발송. Cool sms 우선 씀. 2016. 2017.말에 apibox php7버전 지원한다함.
    | 나중
    */
    /*[group_id] => R1G56D7DF74F02DA
    [success_count] => 1
    [error_count] => 0
    [result_code] => 00
    [result_message] => Success*/


    public function getForm($data = null)
    {
        echo "<form action='/sms/send'>
			<input type='text' name='from' value='1661-5521' />
			<input type='text' name='to' value='010-4775-0852' />
			<input type='text' name='text' value='테스트...' />
			<input type='text' name='type' value='SMS' />
			<input type='submit' value='submit' />
		</form>
		";
    }

    // SK가 작성함. 하수 코딩. 제대로 고쳐야.. ㅜ

    /*유저 페이지에서 post나 get으로 불려진 경우, 그 값 사용*/
    /*public function postSend()
    {

    }*/

    public function getSend($data = null)
    {
        return "Not this way;";
    }
    /*다른 Class에서 send(['to'=>'010...']) 식으로 불려진 경우
      온리 text만 넘길 수도 있음(현재 로그인된 사람에게 문자전송) */
    public static function send($data = null)
    {
        if( !$data ) return false;
        /*if( empty($data['to']) && empty(Auth::user()->userInfo->phone)) return 0; //'받는사람 없음.';*/
        $default = [
            'text'=>'샘플 문자입니다. SK',
            'from'=>'1661-5521',
            'to'=> '010-4775-0852', /*Auth::user()->userInfo->phone*/
        ];
        if(!isset($data['from'])) $data['from'] = $default['from'];
        if(!isset($data['to'])) $data['to'] = $default['to'];
        if(!isset($data['text'])) $data['text'] = $default['text'];

        $ret = self::sendCoolsms($data);
    }

    /*static으로 쓰기 싫음...... 어떻게 하지...*/
    public static function postSend($data = null)
    {
        if( count(Request::all()) ) $data = Request::all();
        //return self::send($data);
        if( empty($data['to']) && empty(Auth::user()->userInfo->phone)) return 0; //'받는사람 없음.';

        $default = [
            'text'=>'빈 메세지입니다.',
            'from'=>'1661-5521',
            'to'=> '01047750852', /*Auth::user()->userInfo->phone*/
        ];
        if(!isset($data['from'])) $data['from'] = $default['from'];
        if(!isset($data['to'])) $data['to'] = $default['to'];
        if(!isset($data['text'])) $data['text'] = $default['text'];
        /*$data['to_user_id'] = 1, 4, 66, 233 식으로 받음*/

        $ret = self::sendCoolsms($data);

        /*{#248 ▼
        +"group_id": "R1G56E7E99824235"
        +"success_count": 1
        +"error_count": 0
        +"result_code": "00"
        +"result_message": "Success"}*/

        // Return the message object to the browser as JSON
        if( isset($data['goto']) ){
            return redirect( $data['goto'] );
        }else { //else return redirect()->back(); // get방식으로 들어왔을때, 순환오류 위험함.
            return $ret->success_count;
        }
    }

    /*얘는 무조건 data값을 정상적으로 받아야 함*/
    /*static으로 쓰기 싫음...... 어떻게 하지...*/
    private static function sendCoolsms($data) /*$to='', $from, $text, $goto = ''*/
    {

        if( empty($data['from']) || empty($data['to']) || empty($data['text']) ) return false;

        //$senderid = $rest->get_senderid_list($options)->getResult();
        $options = new \stdClass();
        $options->from = $data['from'];
        $options->to   = $data['to'];
        $options->text = $data['text'];
        //단문 MMS 자동 구분 // 80자?이상시 강제로 LMS로 변경해야.
        if( mb_strwidth( $options->text , "UTF-8") > 90 ){ $options->type = 'LMS';}
        else $options->type = "SMS";

        /*http://www.coolsms.co.kr/SDK_PHP_Examples_ko
        $options->type = "MMS";
        //type 은 default 가 sms 로 되있으므로 mms 로 바꾸어 주어야 사진이 보내집니다.
        $options->image = "myPic.jpg";*/

        self::recordHistory($options, $data); // 기록허장.     $this->

        /*$rest = (object)  array('success_count' => '9999'); // Test시
        return $rest;*/

        $rest = new Coolsms();
        return $rest->send($options);
    }

    /*이력 DB 기록*/
    private static function recordHistory($options, $data){

        /*$sms_history = new SmsHistory();
        $sms_history->from = $options->from;
        $sms_history->to = $options->to;
        $sms_history->text = $options->text;
        $sms_history->type = $options->type;
        $sms_history->send_user_id = Auth::user()->id;
        if(isset($data['to_user_id'])) {
            $sms_history->user_id = $data['to_user_id'];
        }

        $sms_history->save();*/
    }

    public function getSentCoolsms(){
        $options = new \stdClass();
        /*$options->mid = 'VAXZxcasd1a';                 //Message ID
        $options->gid = 'AVCXZ124ASD';                 //Group ID*/
        $options->count = '10';                 //default 는 20개로 되있습니다.
        $options->page = '3';                   //전체 전송결과 갯수 / count 로 나온 페이지 중에 자신이 확인하고 싶은 페이지 선택
        //$options->s_rcpt = '01000000000';              //발송번호 검색
        $options->s_start = '2016-03-01 00:00:00';     //검색 시작일
        $options->s_end = '2016-3-31 24:59:59';       //검색 끝나는일

        $rest = new Coolsms();
        $result = $rest->sent($options);

        dd( $result);
    }

    public function getBalanceCoolsms()
    {
        $rest = new Coolsms();
        $result = $rest->balance();
        print_r( $result);
        //return $result;
    }

    /*예약 발송 취소하기*/
    public function getCancelBookCoolsms()
    {
        $options = new \stdClass();
        $options->mid = ''; // 예약건 message_id를 DB에 저장해놓아야함.. 예) R1M56E7EC4BB20AA
        //$options->gid = 'zxcvas2asdvc';
        //mid 나 gid 둘중에 하나만 넣으시면 됩니다.
        $rest = new Coolsms();
        $rest->cancel($options);
        //cancel 은 결과값이 없습니다.
    }





    /**
     * @return Response
     */
    public function index()
    {
        return view('home');


        /*메일 보내는 방법
        $data['message'] = '김수로님';
        Mail::send('emails.welcome', $data, function($message)
        {
            $message->to('sangemi@daum.net', 'John Smith')->subject('[분쟁제로] 사이트 이용방법');
        });*/

    }


}
