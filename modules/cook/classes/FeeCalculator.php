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

        $종이값["A1"] = $종이값["A0"] / 2;
        $종이값["A2"] = $종이값["A0"] / 4;
        $종이값["A3"] = $종이값["A0"] / 8;
        $종이값["A4"] = $종이값["A0"] / 16;
        $종이값["A5"] = $종이값["A0"] / 32;
        $종이값["A6"] = $종이값["A0"] / 64;

        $종이값["B1"] = $종이값["B0"] / 2;
        $종이값["B2"] = $종이값["B0"] / 4;
        $종이값["B3"] = $종이값["B0"] / 8;
        $종이값["B4"] = $종이값["B0"] / 16;
        $종이값["B5"] = $종이값["B0"] / 32;
        $종이값["B6"] = $종이값["B0"] / 64;

        $selected = $종이값["B6"];
        return $selected;
    }
    public $fee, $userSetting;
    public $danka, $quantity;
    function __construct(){
        $this->userSetting = array();
        $this->danka['제본'] = array(
            '일반' => 210,
            '무선' => 200,
            '중철' => 100,
            '스프링' => 300, /*1000권 이하는 무조건 30만원, 3천권하면 A6 250원 A5 280원*/
            '라벨링' => 400,
            '떡' => 250,
            '양장' => 700,
            '반양장' => 600,
        );
        $this->danka['용지'] = array( /*국전(A1) 500장(1연)*/
            '미색모조 70g' => 22222, 
            '미색모조 80g' => 26384,
            '미색모조 100g' => 32694, 
            '미색모조 120g' => 44444,
            '백색모조 70g' => 22222,
            '백색모조 80g' => 25616,
            '백색모조 100g' => 32694,
            '백색모조 120g' => 36000,
            '스노우 일반' => 49999,
            '스노우 100g' => 22000, /*노트의달인*/
            '스노우 120g' => 29000, /*노트의달인*/
            '스노우 150g' => 30000, /*노트의달인*/
            '스노우 180g' => 33000, /*노트의달인*/
            '스노우 200g' => 36000, /*노트의달인*/
            '스노우 250g' => 41000, /*노트의달인*/
            '스노우 300g' => 45000, /*노트의달인*/
            '크라프트' => 44444,
            '특수지' => 55555,

        );
        $this->danka['데코'] = array(
            '라운딩' => 300, /**/
            'OPP포장' => 50, /**/
            '타공' => 150, /**/
            '타공링' => 150, /**/
            '오시' => 150, /**/
            '미싱' => 200, /*절취선*/
            '패턴코팅' => 150, /**/
            '도무송' => 200, /**/
            '박' => 200, /*금박 은박 먹박 청박 적박 녹박 홀로그램박 */
            '형압' => 200, /**/
            '먹박' => 200, /**/
        );
        $this->danka['표지인쇄'] = array( /*1R 비용. 보통 1도 1R(1장 16매=8권x500장=>4000권)에 10000원(내지) ~ 15000원(표지)*/
            '단면 1도' => 10000,
            '양면 1도' => 20000, /*2배*/
            '단면 4도' => 40000, /*4배*/
            '양면 4도' => 80000, /*8배*/
        );
        $this->danka['내지인쇄'] = array( /*1R 비용. 보통 1도 1R(1장 16매x500장=>8000장)에 6000원(내지) ~ 10000원(표지)*/
            '단면 1도' => 8000,
            '양면 1도' => 16000, /*2배*/
            '단면 4도' => 32000, /*4배*/
            '양면 4도' => 64000, /*8배*/
        );
        $this->danka['판비'] = array(
            '국-2절' => 10000,
            '46-2절' => 10000,
            '실비' => 8000, /*실제 이정도*/
        );
        /*   접지   에폭시
        */
    }
    /*표지 비용 = 용지비 + 인쇄비 + 판비
    판비    도당 1판이 필요. 먹1도면 1개 필요. 양면이면 판이 2개이므로 2개
            4도가 풀컬러. 앞뒤가 다르고 풀컬러이면 8만원*/
    function feeCoverPaper($real = "real", $paper_name = null, $size = null) // 내부확인용
    {   /* 표지는 1권에 2장 */
        if($real == "real") {
            //$용지비 = $this->quantity * 2 * $this->feePaper1page($this->userSetting["표지-용지"]); // R수(연수) 상관없이 실제 들어가는 종이비.
            $용지비 = ceil($this->numNeededR("표지")) * $this->danka["용지"][$this->userSetting["표지-용지"]];
        }
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
    function feeCoverPan($real = "real", $size = "국-2절"){
        if($real == "real"){
            $판비 = $this->danka["판비"]["국-2절"];
        }else{
            $판비 = $this->danka["판비"]["국-2절"];
        }
        return $판비;
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
            $fee1page = $this->danka['용지'][$paper_name];
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
