<?php namespace Cook\Classes;

use App;

/**
 * The Cook controller class.
 * 가격정보를 DB에 저장해서 여기서 다 불러옴
 *
 * @package october\cms
 * @author Kenny
 */
class FeeCalculator
{
    public static  function feeNote(){

        $종이값["A0"] = 1450;
        $종이값["B0"] = 2050;

        $종이값["A1"] = $종이값["A0"] / 2; /* 국전 */
        $종이값["A2"] = $종이값["A0"] / 4;
        $종이값["A3"] = $종이값["A0"] / 8;
        $종이값["A4"] = $종이값["A0"] / 16;
        $종이값["A5"] = $종이값["A0"] / 32;
        $종이값["A6"] = $종이값["A0"] / 64;

        $종이값["B1"] = $종이값["B0"] / 2; /* 46절 */
        $종이값["B2"] = $종이값["B0"] / 4;
        $종이값["B3"] = $종이값["B0"] / 8;
        $종이값["B4"] = $종이값["B0"] / 16;
        $종이값["B5"] = $종이값["B0"] / 32;
        $종이값["B6"] = $종이값["B0"] / 64;

        /*$selected = $종이값["B6"];
        return $selected;*/
    }
    public $fee, $userSetting;
    public $danka, $quantity;
    function __construct(){
        $this->userSetting = array();

        $this->danka["공장간배송"] = 16000;

        $this->danka['제본'] = array(
            '일반' => 340,
            '무선' => 340, /*35만원 기본이고, 1천권 더할때마다 5만원씩 추가 (달인)*/
            '중철' => 110,
            '스프링' => 350, /*1000권 이하는 무조건 30만원, 3천권하면 A6 250원 A5 280원*/
            '라벨링' => 420, /*스프링작업비만 (제품가격 및 조립비는 데코에서)*/
            '떡' => 310,
            '양장' => 700,
            '반양장' => 600,
        );
        /* 아................ 이런. 제본비는 내지 수량따라서 또 차등이 있네. ㅠㅠ */
        $this->danka_auto['무선'] = array( /*수량상관없이 기본  */
            '0' => array( /* 0권은 기본비용. 초과시 다음 기본권수까지 자동 증가 */
                '기본비용-A4' => "40000",
                '기본비용-B5' => "39000",
                '기본비용-A5' => "38000",
                '기본비용-B6' => "37000",
                '기본비용-A6' => "35000",
            ),
            '100' => array(
                '기본비용-A4' => "255000",
                '기본비용-B5' => "238000",
                '기본비용-A5' => "210000",
                '기본비용-B6' => "200000",
                '기본비용-A6' => "190000",
            ),
            '1000' => array( /*아.... 기본 제본비는 내지 100장을 기준으로 해야하나..... */
                '기본비용-A4' => "550000", '초과비용-A4' => "70", /*1000권 초과부터는 1천권당 7만 원 */
                '기본비용-B5' => "480000", '초과비용-B5' => "70",
                '기본비용-A5' => "400000", '초과비용-A5' => "60",
                '기본비용-B6' => "310000", '초과비용-B6' => "60",
                '기본비용-A6' => "300000", '초과비용-A6' => "60",
            ),
        );
        $this->danka_auto['스프링'] = array( /*수량상관없이 기본  */
            '0' => array( /* 0권은 기본비용. 초과시 다음 기본권수까지 자동 증가 */
                '기본비용-A4' => "50000",
                '기본비용-B5' => "49000",
                '기본비용-A5' => "48000",
                '기본비용-B6' => "47000",
                '기본비용-A6' => "45000",
            ),
            '100' => array(
                '기본비용-A4' => "265000",
                '기본비용-B5' => "245000",
                '기본비용-A5' => "230000",
                '기본비용-B6' => "220000",
                '기본비용-A6' => "205000",
            ),
            '1000' => array( /*1000권 이하는 무조건 30만원, 3천권하면 A6 250원 A5 280원*/
                '기본비용-A4' => "450000", '초과비용-A4' => "350",
                '기본비용-B5' => "410000", '초과비용-B5' => "330",
                '기본비용-A5' => "380000", '초과비용-A5' => "310",
                '기본비용-B6' => "350000", '초과비용-B6' => "290",
                '기본비용-A6' => "330000", '초과비용-A6' => "280",
            ),
        );
        /*4페이지로 접을때, 8페이지, 16페이지로 접을떄가 있음. 이걸 한 꼭지라함.
        40페이지이면 16, 16, 8로 가던지 8꼭지를 x 5번하든지.
        꼭지당 10원.
        40페이지를 8P접기로 제작할 경우 5꼭지가 나옴. 그러면 5x 10원
        50원 x 500권수 = 25000 이 됨. 8만원 이하면 무조건 8만원 */
        $this->danka_auto['중철'] = array( /*수량상관없이 기본 */
            '0' => array( /* 0권은 기본비용. 초과시 다음 기본권수까지 자동 증가 */
                '기본비용-A4' => "90000",
                '기본비용-B5' => "88000",
                '기본비용-A5' => "87000",
                '기본비용-B6' => "86000",
                '기본비용-A6' => "85000",
            ),
            '100' => array(
                '기본비용-A4' => "150000",
                '기본비용-B5' => "145000",
                '기본비용-A5' => "144000",
                '기본비용-B6' => "142000",
                '기본비용-A6' => "138000",
            ),
            '1000' => array( /*1000권 이하는 무조건 30만원, 3천권하면 A6 250원 A5 280원*/
                '기본비용-A4' => "350000", '초과비용-A4' => "25",
                '기본비용-B5' => "310000", '초과비용-B5' => "24",
                '기본비용-A5' => "280000", '초과비용-A5' => "23",
                '기본비용-B6' => "250000", '초과비용-B6' => "22",
                '기본비용-A6' => "230000", '초과비용-A6' => "21",
            ),
        );
        $this->danka_auto['양장'] = array( /*수량상관없이 기본  */
            '0' => array(
                '기본비용-A4' => "50000",
                '기본비용-B5' => "49000",
                '기본비용-A5' => "48000",
                '기본비용-B6' => "47000",
                '기본비용-A6' => "45000",
            ),
            '100' => array(
                '기본비용-A4' => "365000",
                '기본비용-B5' => "345000",
                '기본비용-A5' => "330000",
                '기본비용-B6' => "320000",
                '기본비용-A6' => "305000",
            ),
            '1000' => array( /*1000권 이하는 무조건 30만원, 3천권하면 A6 250원 A5 280원*/
                '기본비용-A4' => "1100000", '초과비용-A4' => "900",
                '기본비용-B5' => "1010000", '초과비용-B5' => "850",
                '기본비용-A5' => "980000", '초과비용-A5' => "840",
                '기본비용-B6' => "950000", '초과비용-B6' => "820",
                '기본비용-A6' => "910000", '초과비용-A6' => "800",
            ),
        );

        /*국전(A1보다 아주약간 큼) 500장(1연)*/
        $this->danka['용지-국전'] = array( /*대유기획 홈피. korea-a.com = http://appleto.n4.cc/etc/paper1.htm*/
            '' => 0,
            ' ' => 0,
            '모조 180g' => 54500, /*표지에서 그냥 모조지 선택시*/
            '모조 250g' => 71500,
            '모조 300g' => 88400,

            '백색모조 70g' => 24700,
            '백색모조 80g' => 26500,
            '백색모조 100g' => 33700,
            '백색모조 120g' => 38600,
            '백색모조 150g' => 46500,
            '백색모조 180g' => 56500,
            '백색모조 220g' => 70600,
            '백색모조 260g' => 84400,

            '미색모조 70g' => 24500,
            '미색모조 80g' => 26584,
            '미색모조 100g' => 32714,
            '미색모조 120g' => 38500,

            '아트' => 27000,
            '아트 70g' => 23500,
            '아트 80g' => 27655,
            '아트 100g' => 33000,
            '아트 120g' => 38000,
            '아트 150g' => 47000,
            '아트 180g' => 57000,
            '아트 200g' => 66000,
            '아트 250g' => 84000,
            '아트 300g' => 99000,

            '스노우 일반' => 49999,
            '스노우 100g' => 33000,
            '스노우 120g' => 38000,
            '스노우 150g' => 47000,
            '스노우 180g' => 57000,
            '스노우 200g' => 66000,
            '스노우 250g' => 84000,
            '스노우 300g' => 99000,

            '크라프트' => 250000,
            '크라프트 180g' => 218000,
            '크라프트 220g' => 218000,
            '크라프트 250g' => 251000,
            '크라프트 300g' => 286000,
            '크라프트 410g' => 382000,

            '특수지' => 257000, /*DB로 관리해야할듯...*/
            '특수지 ' => 257000,
            '특수지 80g' => 258000,
            '특수지 100g' => 258000,
            '특수지 120g' => 258000,
            '특수지 180g' => 258000,
            '특수지 250g' => 258000,
            '특수지 300g' => 258000,
        );
        /*46절(B1보다 아주약간 큼) 500장(1연)*/
        $this->danka['용지-46절'] = array( /*대유기획 홈피. korea-a.com = http://appleto.n4.cc/etc/paper1.htm*/
            '' => 0,
            ' ' => 0,
            '모조 180g' => 54500, /*표지에서 그냥 모조지 선택시*/
            '모조 250g' => 71500,
            '모조 300g' => 88400,

            '백색모조 70g' => 38000,
            '백색모조 80g' => 41000,
            '백색모조 100g' => 48000,
            '백색모조 120g' => 52000,
            '백색모조 150g' => 64000,
            '백색모조 180g' => 78000,
            '백색모조 220g' => 99000,
            '백색모조 260g' => 118000,

            '미색모조 70g' => 38900,
            '미색모조 80g' => 42800,
            '미색모조 100g' => 49500,
            '미색모조 120g' => 54700,

            '아트' => 46500,
            '아트 70g' => 38500,
            '아트 80g' => 41500,
            '아트 100g' => 46500,
            '아트 120g' => 53500,
            '아트 150g' => 66700,
            '아트 180g' => 81000,
            '아트 200g' => 94000,
            '아트 250g' => 124500,
            '아트 300g' => 143100,

            '스노우 일반' => 53500,
            '스노우 100g' => 46500,
            '스노우 120g' => 53500,
            '스노우 150g' => 66700,
            '스노우 180g' => 81000,
            '스노우 200g' => 94000,
            '스노우 250g' => 124500,
            '스노우 300g' => 143100,

            '크라프트' => 250000,
            '크라프트 180g' => 218000,
            '크라프트 220g' => 218000,
            '크라프트 250g' => 251000,
            '크라프트 300g' => 286000,
            '크라프트 410g' => 382000,

            '특수지' => 257000, /*DB로 관리해야할듯...*/
            '특수지 ' => 257000,
            '특수지 80g' => 258000,
            '특수지 100g' => 258000,
            '특수지 120g' => 258000,
            '특수지 180g' => 258000,
            '특수지 250g' => 258000,
            '특수지 300g' => 258000,
        );

        $this->danka['박'] = array( /*기본 5만원 */
            '종이에박' => 50, /*금박 은박 먹박 청박 적박 녹박 홀로그램박 */
            '노트에박' => 250, /**/
            '형압' => 200, /**/
            'explain' => "* 노트달인 : 기본15만, 1천권추가에 3만원(개당30원)
                          * 쿡어노트 : 500권까지 250원/장 / 1000권까지 150원/장",
        );
        $this->danka_easy['박'] = array( /*기본 5만원 */
            '기본비용' => "80000", /*수량상관없이 기본  */
            '기본수량' => "1000",
            '1000' => "30", /*1000권 초과부터는 개당 원 */
        );
        $this->danka['박개수'] = array( // 박가격에 크기따라 배수를 곱함
            '' => 1, /*금박 은박 먹박 청박 적박 녹박 홀로그램박 */
            '1개' => 1,
            '2개' => 2, /**/
            '3개' => 3, /**/
            '4개' => 4, /**/
            '전체' => 5,
        );
        $this->danka['표지인쇄'] = array( /*1R 비용. 보통 1도 1R(1장 16매=8권x500장=>4000권)에 10000원(내지) ~ 15000원(표지)*/
            '' => 0,
            '무지' => 0,
            '단면 1도' => 15000,
            '양면 1도' => 30000, /*2배*/
            '단면 4도' => 60000, /*4배*/
            '양면 4도' => 120000, /*8배*/
        );
        $this->danka['내지인쇄'] = array( /*1R 비용. 보통 1도 1R(1장 16매x500장=>8000장)에 6000원(내지) ~ 10000원(표지)*/
            '' => 0,
            '무지' => 0,
            '단면 1도' => 8000,
            '양면 1도' => 16000, /*2배*/
            '단면 4도' => 32000, /*4배*/
            '양면 4도' => 64000, /*8배*/
        );
        $this->danka['판비'] = array( /* 1도당 판1개*/
            '' => 0,
            '무지' => 0,
            '단면 1도' => 20000,
            '양면 1도' => 20000,
            '단면 4도' => 40000, /**/
            '양면 4도' => 40000, /**/
            '실비' => 8000, /*실제 이정도*/
        );
        $this->danka_easy['유광코팅'] = array(
            '기본비용' => "50000", /*수량상관없이 기본  */
            '기본수량' => "1000",
            '1000' => "15", /*1000권 초과부터는 개당 원 */
        );
        $this->danka_easy['무광코팅'] = array(
            '기본비용' => "60000", /*수량상관없이 기본  */
            '기본수량' => "1000",
            '1000' => "15", /*1000권 초과부터는 개당 원 */
        );
        $this->danka_easy['엠보코팅'] = array(
            '기본비용' => "150000", /*수량상관없이 기본  */
            '기본수량' => "1000",
            '1000' => "30", /*1000권 초과부터는 개당 원 */
        );
        $this->danka_auto['합지'] = array( /*수량상관없이 기본  */
            '0' => array(
                '기본비용-2합' => "80000",
                '기본비용-3합' => "120000",
            ),
            '100' => array(
                '기본비용-2합' => "100000",
                '기본비용-3합' => "140000",
            ),
            '1000' => array( /*1000권 이하는 무조건 30만원, 3천권하면 A6 250원 A5 280원*/
                '기본비용-2합' => "160000", '초과비용-2합' => "160",
                '기본비용-3합' => "220000", '초과비용-3합' => "220",
            ),
        );

        $this->danka_auto['싸바리'] = array( /*수량상관없이 기본  */
            '0' => array(
                '기본비용-일반' => "80000",
            ),
            '100' => array(
                '기본비용-일반' => "110000",
            ),
            '1000' => array( /*1000권 이하는 무조건 30만원, 3천권하면 A6 250원 A5 280원*/
                '기본비용-일반' => "330000", '초과비용-일반' => "500",
            ),
        );

        $this->danka_auto['삽지배치'] = array( /* 삽지를 4, 8, 12 페이지에 삽입시 */
            '0' => array(
                '기본비용-첫장' => "0",
                '기본비용-배수삽입' => "40000",
            ),
            '100' => array(
                '기본비용-첫장' => "0",
                '기본비용-배수삽입' => "50000",
            ),
            '1000' => array( /**/
                '기본비용-첫장' => "0", '초과비용-첫장' => "0",
                '기본비용-배수삽입' => "150000", '초과비용-배수삽입' => "150",
            ),
        );

        $this->danka_easy['일반날개'] = array(
            '기본비용' => "50000", /*수량상관없이 기본  */
            '기본수량' => "1000",
            '1000' => "20", /*1000권 초과부터는 개당 원 */
        );
        $this->danka_easy['포켓날개'] = array(
            '기본비용' => "100000", /*수량상관없이 기본  */
            '기본수량' => "1000",
            '1000' => "40", /*1000권 초과부터는 개당 원 */
        );

        $this->danka['데코'] = array(
            '타공' => 150, /**/
            '타공링' => 150, /**/
            '오시' => 150, /**/
            '미싱' => 200, /*절취선*/
            '패턴코팅' => 150, /**/
            '도무송' => 200, /**/
        );
        $this->danka['라벨스틱'] = array(
            '' => 0,
            '일반' => 200, /**/
            '라벨스틱' => 200, /**/
        );

        $this->danka['OPP'] = array(
            '' => 0,
            '개별OPP포장' => 100, /**/
        );
        $this->danka['라운딩'] = array(
            '' => 0,
            '일반' => 200, /**/
            '4모서리 라운딩' => 200, /**/
        );
        $this->danka['배송'] = array(
            '' => 0,
            '일반' => 50000, /**/
            '트럭' => 50000, /**/
        );

        $this->danka['디자인'] = array(
            '' => 0,
            '제작만' => 0, /**/
            '디자인보완' => 100000, /**/
            '디자인기획' => 200000, /**/
        );
        $this->danka['노임'] = array(
            '' => 0,
            '일반' => 100000, /*노임 무조건 10만 더함*/
            '전화교습' => 300000, /* 각 단계별 절차 전화줌 */
            '방문교습' => 600000, /* 각 단계별 절차 알려줌 */
        );
        /*   접지   에폭시
        */
    }
    /*표지 비용 = 용지비 + 인쇄비 + 판비
    판비    도당 1판이 필요. 먹1도면 1개 필요. 양면이면 판이 2개이므로 2개
            4도가 풀컬러. 앞뒤가 다르고 풀컬러이면 8만원*/
    function feeCoverPaper($real = "real", $paper_name = null, $size = null) // 내부확인용
    {   /* 표지는 1권에 2장 */
        if( ! $this->quantity ) return 0;
        if($real == "real") {
            if( $this->userSetting['크기'][0] == "A" ) $필요한용지 = "국전";
            else if( $this->userSetting['크기'][0] == "B" ) $필요한용지 = "46절";
            //$용지비 = $this->quantity * 2 * $this->feePaper1page($this->userSetting["표지-용지"]); // R수(연수) 상관없이 실제 들어가는 종이비.

            $용지비 = /*ceil*/($this->numNeededR("표지"))
                * $this->danka["용지-".$필요한용지][$this->userSetting["표지-용지"]." ".$this->userSetting["표지-평량"]];
            if (0 < $용지비 && $용지비  < 50000) $용지비 = $용지비*0.5 + 40000;
        }
        $용지비 = $용지비 + $this->danka["공장간배송"];
        return $용지비 ;
    }
    /*표지-인쇄 / 표지는 1권에 2장*/
    function feeCoverPrint($real = "real", $paper_name = null, $size = null) {   /*  */
        if($real == "real") {
            $인쇄비 = ceil($this->numNeededR("표지")) * $this->danka["표지인쇄"][$this->userSetting["표지-인쇄"]];
        }
        return $인쇄비;
    }
    /*표지-판비 // 국-2절 or 46-2절 */
    /* 판 1개에 A5짜리 16개를 올림. 따라서 16장 내지를 완전 다르게 해도, */
    function feeCoverPan($real = "real", $size = "국-2절"){
        if( ! $this->quantity ) return 0;
        if($real == "real"){
            $판비 = $this->danka["판비"][$this->userSetting["표지-인쇄"]];
        }else{

        }
        return $판비;
    }
    /*표지-박비 //  */
    function feeCoverBak($real = "real", $size = "국-2절"){
        if( $this->userSetting["표지-박개수"] ){
            $기본비 = $this->danka_easy["박"]["기본비용"]
                    * $this->danka["박개수"][$this->userSetting["표지-박개수"]];
            $기본수량 = $this->danka_easy["박"]["기본수량"];
            if($real == "real"){
                if( $this->quantity <= $기본수량){
                    $박비 = $기본비;
                }else{ // 기본 1천권 넘으면 / 초과 권당
                    $박비 = $기본비
                        + ($this->quantity - $기본수량 ) * $this->danka_easy["박"]["1000"];
                    // 이부분 완벽하지 않음. 1만개 이상할 때 등 할인 들어가야.
                }
            }else{
            }
            $박비 = $박비 + $this->danka["공장간배송"];

            return $박비;
        }
    }
    /*표지-코팅비  */
    function feeCoverCoating($real = "real"){ /* 유광, 무광, 엠보 */
        if( $this->userSetting["표지-코팅"] ){
            $기본비 = $this->danka_easy[$this->userSetting["표지-코팅"]]["기본비용"];
            $기본수량 = $this->danka_easy[$this->userSetting["표지-코팅"]]["기본수량"];
            if($real == "real"){
                if( $this->quantity <= $기본수량 ){
                    $코팅비 = $기본비;
                }else{ // 기본 1천권 넘으면 / 초과 권당
                    $코팅비 = $기본비
                        + ($this->quantity - $기본수량 ) * $this->danka_easy[$this->userSetting["표지-코팅"]]["1000"];
                }
            }else{
            }
            $코팅비 = $코팅비 + $this->danka["공장간배송"];

            return $코팅비;
        }
    }
    /*표지-날개  */
    function feeCoverWing($real = "real", $type = "일반"){ /* 일반, 포켓 */
        if( $this->userSetting["표지-날개"] ){
            $기본비 = $this->danka_easy[$type."날개"]["기본비용"];
            $기본수량 = $this->danka_easy[$type."날개"]["기본수량"];
            if($real == "real"){
                if( $this->quantity <= $기본수량 ){
                    $날개비 = $기본비;
                }else{ // 기본 1천권 넘으면 / 초과 권당
                    $날개비 = $기본비
                        + ($this->quantity - $기본수량 ) * $this->danka_easy[$type."날개"]["1000"];
                }
            }else{
            }
            return $날개비;
        }
    }

    /* 삽지 ==========================================*/
    /*비용 = 용지비 + 인쇄비 + 판비
    판비    도당 1판이 필요. 먹1도면 1개 필요. 양면이면 판이 2개이므로 2개
            4도가 풀컬러. 앞뒤가 다르고 풀컬러이면 8만원*/
    function feeIntroPaper($real = "real", $paper_name = null, $size = null) // 내부확인용
    {
        if($real == "real") {
            if( $this->userSetting['크기'][0] == "A" ) $필요한용지 = "국전";
            else if( $this->userSetting['크기'][0] == "B" ) $필요한용지 = "46절";

            $용지비 = ceil($this->numNeededR("삽지"))
                * $this->danka["용지-".$필요한용지][$this->userSetting["삽지-용지"]." ".$this->userSetting["삽지-평량"]];
        }
        return $용지비 ;
    }
    /*삽지-인쇄 */
    function feeIntroPrint($real = "real", $paper_name = null, $size = null) {   /*  */
        if($real == "real") {
            $인쇄비 = ceil($this->numNeededR("삽지")) * $this->danka["표지인쇄"][$this->userSetting["삽지-인쇄"]];
        }
        return $인쇄비;
    }
    /*삽지-판비 // 국-2절 or 46-2절 */
    function feeIntroPan($real = "real", $size = "국-2절"){
        if( ! $this->quantity ) return 0;
        if($real == "real"){
            if($this->userSetting["삽지-매수"]){
                $판비 = $this->danka["판비"][$this->userSetting["삽지-인쇄"]];
            }else return 0;
        }else{
        }
        return $판비;
    }
    // 오류있을때 이걸로 확인 // print_r($this->userSetting);
    /*삽지-배치 */
    function feeIntroPosition($real = "real"){
        if( ! $this->quantity ) return 0;
        $일차분류 = "삽지배치"; //

        $이차분류 = $this->userSetting['삽지-배치']; //

        if($real == "real"){
            $수량갭 = array_keys($this->danka_auto[$일차분류]); /*0, 100, 1000권 간격*/
            foreach($수량갭 as $key => $수량갭_){
                /*갭 사이 간격 미리 배열로 저장*/
                /*만약 150권 제작이면, 100권 + auto증가*/
                $수량_갭하단 = $수량갭[$key];
                $비용_갭하단 = $this->danka_auto[$일차분류][$수량갭_]["기본비용-".$이차분류];
                if(  !isset($수량갭[$key+1])) { /*가장 큰구간이면 */
                    $권당증가비용 = $this->danka_auto[$일차분류][$수량갭_]["초과비용-".$이차분류];
                    break;
                }else if( $수량갭[$key] <= $this->quantity && $this->quantity < $수량갭[$key+1]){
                    /*만약 150개주문이면 [1000] 값 */ /*'기본비용-A6' => "35000", '초과비용-A6' => "auto",*/
                    $권당증가비용 = ( $this->danka_auto[$일차분류][$수량갭[$key+1]]["기본비용-".$이차분류]
                            - $비용_갭하단)
                        / ($수량갭[$key+1] - $수량_갭하단 ); /*해당구간의 기울기*/
                    break;
                }
            }
            $배치비 = $비용_갭하단 + $권당증가비용 * ($this->quantity - $수량_갭하단 );
            //$배치비 = $배치비 + $this->danka["공장간배송"];

            return $배치비;
        }
    }

    /* 내지 ==========================================*/
    /*표지 비용 = 용지비 + 인쇄비 + 판비
    판비    도당 1판이 필요. 먹1도면 1개 필요. 양면이면 판이 2개이므로 2개
            4도가 풀컬러. 앞뒤가 다르고 풀컬러이면 8만원*/
    function feeInnerPaper($real = "real", $paper_name = null, $size = null) // 내부확인용
    {   /* 표지는 1권에 2장 */
        if($real == "real") {
            if( $this->userSetting['크기'][0] == "A" ) $필요한용지 = "국전";
            else if( $this->userSetting['크기'][0] == "B" ) $필요한용지 = "46절";

            $용지비 = ceil($this->numNeededR("내지"))
                *$this->danka["용지-".$필요한용지][$this->userSetting["내지-용지"]." ".$this->userSetting["내지-평량"]];
        }
        return $용지비 ;
    }
    /*내지-인쇄 / 표지는 1권에 2장*/
    function feeInnerPrint($real = "real", $paper_name = null, $size = null) {   /*  */
        if($real == "real") {
            $인쇄비 = ceil($this->numNeededR("내지")) * $this->danka["내지인쇄"][$this->userSetting["내지-인쇄"]];
        }
        return $인쇄비;
    }
    /*내지-판비 // 국-2절 or 46-2절 */
    function feeInnerPan($real = "real", $size = "국-2절"){
        if( ! $this->quantity ) return 0;

        if($real == "real"){
            $판비 = $this->danka["판비"][$this->userSetting["내지-인쇄"]];
        }else{
        }
        return $판비;
    }

    /*합지비*/
    function feeCoverHapji($real = "real", $paper_name = null, $size = null)
    {
        if( ! $this->quantity ) return 0;
        if( $this->userSetting['제본'] != '스프링' && $this->userSetting['제본'] != '양장' ){
            return 0; // 스프링만 합지가 됨, 양장은 합지가 기본
        }
        if( $this->userSetting['표지-합지'] == '') return 0; // 합지없음

        $일차분류 = "합지"; //2합, 3합
        $이차분류 = $this->userSetting['표지-합지']; //2합, 3합

        if($real == "real"){
            $수량갭 = array_keys($this->danka_auto[$일차분류]); /*0, 100, 1000권 간격*/
            foreach($수량갭 as $key => $수량갭_){
                /*갭 사이 간격 미리 배열로 저장*/
                /*만약 150권 제작이면, 100권 + auto증가*/
                $수량_갭하단 = $수량갭[$key];
                $비용_갭하단 = $this->danka_auto[$일차분류][$수량갭_]["기본비용-".$이차분류];
                if(  !isset($수량갭[$key+1])) { /*가장 큰구간이면 */
                    $권당증가비용 = $this->danka_auto[$일차분류][$수량갭_]["초과비용-".$이차분류];
                    break;
                }else if( $수량갭[$key] <= $this->quantity && $this->quantity < $수량갭[$key+1]){
                    /*만약 150개주문이면 [1000] 값 */ /*'기본비용-A6' => "35000", '초과비용-A6' => "auto",*/
                    $권당증가비용 = ( $this->danka_auto[$일차분류][$수량갭[$key+1]]["기본비용-".$이차분류]
                            - $비용_갭하단)
                        / ($수량갭[$key+1] - $수량_갭하단 ); /*해당구간의 기울기*/
                    break;
                }
            }
            $합지비 = $비용_갭하단 + $권당증가비용 * ($this->quantity - $수량_갭하단 );
            $합지비 = $합지비 + $this->danka["공장간배송"];

            return $합지비;
        }
    }

    /*싸바리비*/
    function feeCoverSsabari($real = "real", $paper_name = null, $size = null)
    {
        if( ! $this->quantity ) return 0;
        if( $this->userSetting['제본'] != '스프링' && $this->userSetting['제본'] != '양장' ){
            return 0; // 스프링만 싸바리가 됨, 양장은 싸바리가 기본
        }
        if( $this->userSetting['표지-싸바리'] == '') return 0; // 합지없음

        $일차분류 = "싸바리"; //2합, 3합
        $이차분류 = $this->userSetting['표지-싸바리']; //일반

        if($real == "real"){
            $수량갭 = array_keys($this->danka_auto[$일차분류]); /*0, 100, 1000권 간격*/
            foreach($수량갭 as $key => $수량갭_){
                /*갭 사이 간격 미리 배열로 저장*/
                /*만약 150권 제작이면, 100권 + auto증가*/
                $수량_갭하단 = $수량갭[$key];
                $비용_갭하단 = $this->danka_auto[$일차분류][$수량갭_]["기본비용-".$이차분류];
                if(  !isset($수량갭[$key+1])) { /*가장 큰구간이면 */
                    $권당증가비용 = $this->danka_auto[$일차분류][$수량갭_]["초과비용-".$이차분류];
                    break;
                }else if( $수량갭[$key] <= $this->quantity && $this->quantity < $수량갭[$key+1]){
                    /*만약 150개주문이면 [1000] 값 */ /*'기본비용-A6' => "35000", '초과비용-A6' => "auto",*/
                    $권당증가비용 = ( $this->danka_auto[$일차분류][$수량갭[$key+1]]["기본비용-".$이차분류]
                            - $비용_갭하단)
                        / ($수량갭[$key+1] - $수량_갭하단 ); /*해당구간의 기울기*/
                    break;
                }
            }
            $싸바리비 = $비용_갭하단 + $권당증가비용 * ($this->quantity - $수량_갭하단 );
            $싸바리비 = $싸바리비 + $this->danka["공장간배송"];

            return $싸바리비;
        }
    }

    /*총 제본비*/
    function feeJebon($real = "real", $paper_name = null, $size = null)
    {
        if( ! $this->quantity ) return 0;
        $제본종류 = $this->userSetting['제본'];
        if($real == "real"){
            $제본단가 = $this->danka['제본'][$제본종류];
            $제본비 = $제본단가 * $this->quantity;

            $수량갭 = array_keys($this->danka_auto[$제본종류]); /*0, 100, 1000권 간격*/
            foreach($수량갭 as $key => $수량갭_){
                /*갭 사이 간격 미리 배열로 저장*/
                /*만약 150권 제작이면, 100권 + auto증가*/
                $수량_갭하단 = $수량갭[$key];
                $비용_갭하단 = $this->danka_auto[$제본종류][$수량갭_]["기본비용-".$this->userSetting['크기']];
                if(  !isset($수량갭[$key+1])) { /*가장 큰구간이면 */
                    $권당증가비용 = $this->danka_auto[$제본종류][$수량갭_]["초과비용-".$this->userSetting['크기']];
                    break;
                }else if( $수량갭[$key] <= $this->quantity && $this->quantity < $수량갭[$key+1]){
                    /*만약 150개주문이면 [1000] 값 */ /*'기본비용-A6' => "35000", '초과비용-A6' => "auto",*/
                    $권당증가비용 = ( $this->danka_auto[$제본종류][$수량갭[$key+1]]["기본비용-".$this->userSetting['크기']]
                            - $비용_갭하단)
                        / ($수량갭[$key+1] - $수량_갭하단 ); /*해당구간의 기울기*/
                    break;
                }
            }
            $제본비 = $비용_갭하단 + $권당증가비용 * ($this->quantity - $수량_갭하단 );

            /*내지수량에 따른 차등... 위 가격은 내지 100장일때였음*/
            if($제본종류 == '무선')       $내지따른가중치 = 0.4;
            elseif($제본종류 == '스프링') $내지따른가중치 = 0.2;
            elseif($제본종류 == '양장')   $내지따른가중치 = 0.1;
            else $내지따른가중치 = 0;
            $제본비 = $제본비 - $제본비 * (100-$this->userSetting['내지-매수'])/100 * $내지따른가중치;

            $제본비 = $제본비 + $this->danka["공장간배송"];

            return $제본비;
            /*if($this->userSetting['제본'] == '무선' || $this->userSetting['제본'] == '스프링'){
                $기본비 = $this->danka_by_amount[$this->userSetting['제본']]["기본비용-".$this->userSetting['크기']];
                $기본수량 = $this->danka_by_amount[$this->userSetting['제본']]["기본수량-".$this->userSetting['크기']];
                    if( $this->quantity <= $기본수량 ){
                        $제본비 = $기본비;
                    }else{ // 기본 1천권 넘으면 / 초과 권당
                        $제본비 = $기본비 +
                            ($this->quantity - $기본수량 )
                            * $this->danka_by_amount[$this->userSetting['제본']]["초과비용-".$this->userSetting['크기']];
                    }
                return $제본비;
            }*/
        }else{
        }
    }

    /*라벨스틱비*/
    function feeLarvelStick($real = "real", $size = '8mm')
    {
        $라벨스틱단가 = $this->danka['라벨스틱'][$this->userSetting['데코-라벨스틱']];
        $라벨스틱비 = $라벨스틱단가 * $this->quantity;
        return $라벨스틱비;
    }
    /*라운딩비*/
    function feeRounding($real = "real", $size = '8mm')
    {
        $라운딩단가 = $this->danka['라운딩'][$this->userSetting['데코-라운딩']];
        $라운딩비 = $라운딩단가 * $this->quantity;
        return $라운딩비;
    }
    /*OPP포장비*/
    function feeOPP($real = "real", $size = '8mm')
    {
        $OPP작업단가 = $this->danka['OPP'][$this->userSetting['데코-OPP']];
        $OPP작업비 = $OPP작업단가 * $this->quantity;
        return $OPP작업비;
    }

    /*디자인*/
    function feeDesign($real = "real")
    {
        $최종디자인비 = $this->danka['디자인'][$this->userSetting['디자인']];
        return $최종디자인비;
    }
    /*배송*/
    function feeShipping($real = "real")
    {
        $배송비 = $this->danka['배송'][$this->userSetting['배송']];
        return $배송비;
    }
    /*노임*/
    function feeWorking($real = "real")
    {
        $노임비 = $this->danka['노임']['일반'];
        /*100권기준으로 증액*/
        $노임비 = $노임비 + $노임비 * $this->quantity / 100 * 0.1;
        return $노임비;
    }






    /*필요한 R(연)수*/
    function numNeededR($표지삽지내지 = "내지"){
        $this->userSetting['표지-매수'] = 2;
        /*if(3합)$this->userSetting['표지-매수'] = 4;*/
        $numPaper = $this->userSetting[$표지삽지내지.'-매수'];

        $cntPaper = pow(2, ($this->userSetting['크기'][1]-1)); // A1하나에 A4는 8장(2^3)나옴
        $neededR = $numPaper * $this->quantity / $cntPaper / 500;
        /*echo $numPage . '-'. $cntPaper.'---';*/
        return $neededR * 1.125; // 여분 +0.125
    }
    /*제본비 1권*/
    function feeJebon1book($paper_name = null, $size = null)
    {
        $jebon = $this->danka['제본'][$this->userSetting['제본']];
        $fee1book = $jebon;
        return $fee1book;
    }
    /*내지 한장의 가격 / 스노우 180g, B5*/
    function feePaper1page($paper_name = null, $size = null){
        if( $size == null ) $size = $this->userSetting['크기'];
        if($paper_name == null){
            return "error!! 55s5";
        }else{
            $fee1page = $this->danka['용지-국전'][$paper_name];
            $cntPaper = pow(2, ($size[1]-1)); // A1하나에 A4는 8장(2^3)나옴
            $fee1page = $fee1page / $cntPaper / 500; //1연
        }
        // A시리즈인지 B시리즈인지
        if($size[0] == 'A'){
        }else if($size[0] == 'B'){
        }
        return $fee1page;
    }

    function getUserSetting(){
        return $this->userSetting;
    }
    function setQuantity($v){
        $this->quantity = $v;
    }
    /*권당 가격*/
    function feeEach(){
        $final = 0;
        foreach($this->userSetting as $key => $val){
            /*    if(isset($this->danka[$cate][$value]))
                $fee = $this->danka[$cate][$value];
            else $fee = 0;

            $final += $val['fee'];        */
        }
        return $final;
    }
    /*각 단계별로 합산 계산*/
    function feeFinal_byStep(){
        $final = $this->feeEach() * $this->quantity;
        if($this->quantity < 1000){
            $final = $final * 1;
        }else if(1000 <= $this->quantity  && $this->quantity < 3000){
            $final = $final * 0.8;
        }else{
            $final = $final * 0.7;
        }
        return $final;
    }
    /*권당 가격으로 수량 계산*/
    function feeFinal_byQuantity(){
        $final = $this->feeEach() * $this->quantity;
        if($this->quantity < 1000){
            $final = $final * 1;
        }else if(1000 <= $this->quantity  && $this->quantity < 3000){
            $final = $final * 0.8;
        }else{
            $final = $final * 0.7;
        }
        return $final;
    }
    function setCate($cate, $value){
        $this->userSetting[$cate] = $value;
        /*
        [제본] => Array
        (
            [title] => 스프링제본
        )*/
    }

    function __destruct(){
        //echo "<br><br>객체 소멸";
    }
}


/*      if (bindv == "stamp") {
          if ((innum > 64) && (ptype <= 100)) {
            alert("내지 100g이하 종이 중철제본은 화보와 내지를 포함하여 64페이지까지만 가능합니다!! 페이지를 줄이시거나 제본방식을 바꾸시기 바랍니다!");
            stopf = 1;
            document.getElementById("picnum").value = 0;
            document.getElementById("notenum").value = 64;
            SelectSize('document.aForm');
          }
           if ((innum > 56) && (ptype == 120)) {
            alert("내지 120g 종이 중철제본은 화보와 내지를 포함하여 56페이지까지만 가능합니다!! 페이지를 줄이시거나 제본방식을 바꾸시기 바랍니다!");
            stopf = 1;
            document.getElementById("picnum").value = 0;
            document.getElementById("notenum").value = 56;
            SelectSize('document.aForm');
          }
          if ((innum > 48) && (ptype == 150)) {
            alert("내지 150g 종이 중철제본은 화보와 내지를 포함하여 48페이지까지만 가능합니다!! 페이지를 줄이시거나 제본방식을 바꾸시기 바랍니다!");
            stopf = 1;
            document.getElementById("picnum").value = 0;
            document.getElementById("notenum").value = 48;
            SelectSize('document.aForm');
          }
      }

*/
