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

        /*$selected = $종이값["B6"];
        return $selected;*/
    }
    public $fee, $userSetting;
    public $danka, $quantity;
    function __construct(){
        $this->userSetting = array();
        $this->danka['제본'] = array(
            '일반' => 310,
            '무선' => 310, /*35만원 기본이고, 1천권 더할때마다 5만원씩 추가 (달인)*/
            '중철' => 110,
            '스프링' => 350, /*1000권 이하는 무조건 30만원, 3천권하면 A6 250원 A5 280원*/
            '라벨링' => 420, /*스프링작업비만 (제품가격 및 조립비는 데코에서)*/
            '떡' => 310,
            '양장' => 700,
            '반양장' => 600,
        );
        $this->danka_easy['스프링'] = array(
            '스프링' => 400, /*1000권 이하는 무조건 30만원, 3천권하면 A6 250원 A5 280원*/
            '기본비용' => "350000", /*수량상관없이 기본  */
            '기본수량' => "1000",
            '1000' => "320", /*1000권 초과부터는 개당 원 */
        );
        /* 현재 A1과 B1 사이즈 가격을 구분하지 않음. 구분해야...*/
        $this->danka['용지'] = array( /*국전(A1) 500장(1연)*/
            '' => 0,
            ' ' => 0,
            '모조 180g' => 34000, /*표지에서 그냥 모조지 선택시*/
            '모조 250g' => 41000,
            '모조 300g' => 45000,

            '미색모조 70g' => 23000,
            '미색모조 80g' => 26384,
            '미색모조 100g' => 32694,
            '미색모조 120g' => 44444,
            '백색모조 70g' => 22222,
            '백색모조 80g' => 25616,
            '백색모조 100g' => 32694,
            '백색모조 120g' => 36000,

            '아트' => 27000,
            '아트 70g' => 23500,
            '아트 80g' => 27384,
            '아트 100g' => 34694,
            '아트 120g' => 45444,
            '아트 180g' => 47000,
            '아트 250g' => 48000,
            '아트 300g' => 49000,

            '스노우 일반' => 49999,
            '스노우 100g' => 23000, /*노트의달인*/
            '스노우 120g' => 30000, /*노트의달인*/
            '스노우 150g' => 31000, /*노트의달인*/
            '스노우 180g' => 34000, /*노트의달인*/
            '스노우 200g' => 37000, /*노트의달인*/
            '스노우 250g' => 42000, /*노트의달인*/
            '스노우 300g' => 46000, /*노트의달인*/

            '크라프트' => 44444,
            '크라프트 180g' => 49000, /*표지에서 선택시*/
            '크라프트 250g' => 51000,
            '크라프트 300g' => 53000,

            '특수지' => 57000,
            '특수지 80g' => 54000, /*표지에서 선택시*/
            '특수지 100g' => 55000,
            '특수지 150g' => 56000,
            '특수지 180g' => 57000,
            '특수지 250g' => 58000,
            '특수지 300g' => 59000,

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
            '단면 1도' => 10000,
            '양면 1도' => 20000, /*2배*/
            '단면 4도' => 40000, /*4배*/
            '양면 4도' => 80000, /*8배*/
        );
        $this->danka['내지인쇄'] = array( /*1R 비용. 보통 1도 1R(1장 16매x500장=>8000장)에 6000원(내지) ~ 10000원(표지)*/
            '' => 0,
            '단면 1도' => 8000,
            '양면 1도' => 16000, /*2배*/
            '단면 4도' => 32000, /*4배*/
            '양면 4도' => 64000, /*8배*/
        );
        $this->danka['판비'] = array( /* 1도당 판1개*/
            '' => 0,
            '단면 1도' => 20000,
            '양면 1도' => 20000,
            '단면 4도' => 40000, /**/
            '양면 4도' => 40000, /**/
            '실비' => 8000, /*실제 이정도*/
        );
        $this->danka_easy['유광코팅'] = array(
            '기본비용' => "35000", /*수량상관없이 기본  */
            '기본수량' => "1000",
            '1000' => "15", /*1000권 초과부터는 개당 원 */
        );
        $this->danka_easy['무광코팅'] = array(
            '기본비용' => "40000", /*수량상관없이 기본  */
            '기본수량' => "1000",
            '1000' => "15", /*1000권 초과부터는 개당 원 */
        );
        $this->danka_easy['엠보코팅'] = array(
            '기본비용' => "150000", /*수량상관없이 기본  */
            '기본수량' => "1000",
            '1000' => "30", /*1000권 초과부터는 개당 원 */
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
            '개별OPP포장' => 50, /**/
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
            $용지비 = ceil($this->numNeededR("표지"))
                * $this->danka["용지"][$this->userSetting["표지-용지"]." ".$this->userSetting["표지-평량"]];
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
    /* 판 1개에 A5짜리 16개를 올림. 따라서 16장 내지를 완전 다르게 해도, */
    function feeCoverPan($real = "real", $size = "국-2절"){
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
            return $박비;
        }
    }
    /*표지-박비  */
    function feeCoverCoating($real = "real"){ /* 유광, 무광, 엠보 */
        if( $this->userSetting["표지-코팅"] ){
            $기본비 = $this->danka_easy[$this->userSetting["표지-코팅"]]["기본비용"];
            $기본수량 = $this->danka_easy[$this->userSetting["표지-코팅"]]["기본수량"];
            if($real == "real"){
                if( $this->quantity <= $기본수량 ){
                    $박비 = $기본비;
                }else{ // 기본 1천권 넘으면 / 초과 권당
                    $박비 = $기본비
                        + ($this->quantity - $기본수량 ) * $this->danka_easy[$this->userSetting["표지-코팅"]]["1000"];
                }
            }else{
            }
            return $박비;
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
    {   /* 표지는 1권에 2장 */
        if($real == "real") {
            //$용지비 = $this->quantity * 2 * $this->feePaper1page($this->userSetting["표지-용지"]); // R수(연수) 상관없이 실제 들어가는 종이비.
            $용지비 = ceil($this->numNeededR("삽지"))
                * $this->danka["용지"][$this->userSetting["삽지-용지"]." ".$this->userSetting["삽지-평량"]];
        }
        return $용지비 ;
    }
    /*표지-인쇄 / 표지는 1권에 2장*/
    function feeIntroPrint($real = "real", $paper_name = null, $size = null) {   /*  */
        if($real == "real") {
            $인쇄비 = ceil($this->numNeededR("삽지")) * $this->danka["표지인쇄"][$this->userSetting["삽지-인쇄"]];
        }
        return $인쇄비;
    }
    /*표지-판비 // 국-2절 or 46-2절 */
    function feeIntroPan($real = "real", $size = "국-2절"){
        if($real == "real"){
            if($this->userSetting["삽지-매수"]){
                $판비 = $this->danka["판비"][$this->userSetting["삽지-인쇄"]];
            }else return 0;
        }else{
        }
        return $판비;
    }


    /* 내지 ==========================================*/
    /*표지 비용 = 용지비 + 인쇄비 + 판비
    판비    도당 1판이 필요. 먹1도면 1개 필요. 양면이면 판이 2개이므로 2개
            4도가 풀컬러. 앞뒤가 다르고 풀컬러이면 8만원*/
    function feeInnerPaper($real = "real", $paper_name = null, $size = null) // 내부확인용
    {   /* 표지는 1권에 2장 */
        if($real == "real") {
            //$용지비 = $this->quantity * 2 * $this->feePaper1page($this->userSetting["표지-용지"]); // R수(연수) 상관없이 실제 들어가는 종이비.
            $용지비 = ceil($this->numNeededR("내지"))
                *$this->danka["용지"][$this->userSetting["내지-용지"]." ".$this->userSetting["내지-평량"]];
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
        if($real == "real"){
            $판비 = $this->danka["판비"][$this->userSetting["내지-인쇄"]];
        }else{
        }
        return $판비;
    }

    /*총 제본비*/
    function feeJebon($real = "real", $paper_name = null, $size = null)
    {
        $제본단가 = $this->danka['제본'][$this->userSetting['제본']];
        $제본비 = $제본단가 * $this->quantity;
        return $제본비;
        $제본단가 = $this->danka_easy['스프링'][$this->userSetting['제본']];

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
