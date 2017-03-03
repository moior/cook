<?php namespace Cooka\Order\Components;

use Cms\Classes\ComponentBase;
use ApplicationException;
use Cook\Classes\FeeCalculator;
use Cooka\Order\Models\Order;
use RainLab\User\Facades\Auth;

class Bill extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Bill for print',
            'description' => 'Implements a simple to-do list.'
        ];
    }

    public function defineProperties()
    {
        return [
            'type' => [
                'description'       => 'Final bill with a signiture, Realtime bill during cooking',
                'title'             => 'Bill type',
                'default'           => 'Final',
                'type'              => 'string',
                'validationPattern' => '^[a-z]+$',
                'validationMessage' => 'Please submit correct Bill type.'
            ]
        ];
    }
    /*디버그할때 이거 부름 >> 잘 안되네.. onRun이 자동으로 불림. 이건 지우라.*/
    public function onDebug()
    {
        $user = $this->user;
        return ($user);

    }


    /*노트 다 조합 후 저장버튼 클릭시*/
    public function onRun()
    {
        // This code will be executed when the page or layout is
        // loaded and the component is attached to it.


        //$this->page['posts'] = post();
        $data = post();
        $this->page['total_data'] = $data; // Inject some variable to the page

        /*
         $obj = json_decode($data, true);
         echo json_encode($obj['data']);*/

        $fee = new FeeCalculator;

        /*paper.note.htm 의 form 안에 있는 정보*/
        /*한번에 $fee->setCate( aa, @data[ aa ] )*/
        foreach($data as $key=> $data_){
            $fee->setCate($key, $data_);
        }
        /*$fee->setCate("제본"      , $data["제본"       ]);//제본에서 라벨링제본은 의미없음. 라벨스틱작업비에서 계산.
        $fee->setCate("크기"      , $data["크기"       ]);
        $fee->setCate("크기-내지" , $data["크기-내지"       ]);
        $fee->setCate("표지-스타일" , $data["표지-스타일"  ]);// 인쇄, 박
        $fee->setCate("표지-방식" , $data["표지-방식"  ]);// 일반, 2합, 3합, 싸바리
        $fee->setCate("표지-용지" , $data["표지-용지"  ]);// 스노우 80g 등
        $fee->setCate("표지-평량" , $data["표지-평량"  ]);// <!--합지 포함 평량-->
        $fee->setCate("표지-코팅" , $data["표지-코팅"  ]);//
        $fee->setCate("표지-박"   , $data["표지-박"    ]);//<!--1천회 기준 10만-->
        $fee->setCate("표지-박개수"   , $data["표지-박개수"    ]);//
        $fee->setCate("표지-날개" , $data["표지-날개"  ]);//

        $fee->setCate("삽지-용지" , $data["삽지-용지"  ]);//
        $fee->setCate("삽지-평량" , $data["삽지-평량"  ]);//
        $fee->setCate("삽지-매수" , $data["삽지-매수"  ]);//

        $fee->setCate("내지-용지" , $data["내지-용지"  ]);// 미색모조 80g 등
        $fee->setCate("내지-평량" , $data["내지-평량"  ]);//
        $fee->setCate("내지-매수" , $data["내지-매수"  ]);//

        $fee->setCate("데코-라벨스틱", $data["데코-라벨스틱"]);//
        $fee->setCate("데코-라운딩", $data["데코-라운딩"]);//
        $fee->setCate("데코-OPP"  , $data["데코-OPP"   ]);

        $fee->setCate("표지-인쇄"      , $data["표지-인쇄"       ]); // 단면4도
        $fee->setCate("삽지-인쇄"      , $data["삽지-인쇄"       ]); // 양면1도
        $fee->setCate("내지-인쇄"      , $data["내지-인쇄"       ]); // 양면1도*/

        $fee->setQuantity( $data['수량'] );
        /*$fee->setCate("수량"      , $data["수량"       ]);*/

        $this->page["수량"] = $data['수량'];

        /*$fee->setCate('제본', $data['제본']);
        $fee->setCate('표지', $data['표지']);
        $fee->setCate('내지', $data['내지']);
        $fee->setCate('크기', $data['크기']);
        $fee->setCate('매수', $data['매수']);
        */

        $this->page["표지_종이값"] = $fee->feeCoverPaper("real"); /*사이즈따른 1장가격 x 표지페이지(보통은2) x 수량 x 종이값*/
        $this->page["표지_인쇄비"] = $fee->feeCoverPrint("real");
        $this->page["표지_판비"] = $fee->feeCoverPan("real");
        $this->page["표지_코팅비"] = $fee->feeCoverCoating("real");
        $this->page["표지_박비"] = $fee->feeCoverBak("real");
        $this->page["표지_합지비"] = $fee->feeCoverHapji("real");
        $this->page["표지_싸바리비"] = $fee->feeCoverSsabari("real");

        $this->page["표지_날개비"] = $fee->feeCoverWing("real");

        if(isset($data['표지_직접1_항목']))
            $this->page["표지_직접1_항목"] = $data['표지_직접1_항목'];
        if(isset($data['표지_직접1_비용']))
            $this->page["표지_직접1_비용"] = $data['표지_직접1_비용'];

        $this->page["삽지_종이값"] = $fee->feeIntroPaper("real");
        $this->page["삽지_인쇄비"] = $fee->feeIntroPrint("real");
        $this->page["삽지_판비"]   = $fee->feeIntroPan("real");

        $this->page["내지_종이값"] = $fee->feeInnerPaper("real");
        $this->page["내지_인쇄비"] = $fee->feeInnerPrint("real");
        $this->page["내지_판비"]   = $fee->feeInnerPan("real");

        $this->page["제본비"] = $fee->feeJebon("real");

        $this->page["라운딩비"] = $fee->feeRounding("real");
        $this->page["포장비"] = $fee->feeOPP("real");
        $this->page["라벨작업비"] = $fee->feeLarvelStick("real");

        $this->page["디자인비"] = $fee->feeDesign("real");
        $this->page["노임비"] = $fee->feeWorking("real");

        $this->page['필요R수_표지'] = round($fee->numNeededR("표지"), 2);
        $this->page['필요R수_삽지'] = round($fee->numNeededR("삽지"), 2);
        $this->page['필요R수_내지'] = round($fee->numNeededR("내지"), 2);
        $this->page['내지한장가격'] = round($fee->feePaper1page(), 1);


        //$this->page['제본비1권'] = ceil($fee->feeJebon1book());

        //$this->page['한권가격'] = $fee->feeEach();
        //$this->page['usersetting'] = $fee->getUserSetting(); // 배열을 확인할 길이 없음..ㅠ
        //$this->page['최종가격_수량으로계산'] = number_format($fee->feeFinal_byQuantity());

        $final = $this->page["표지_종이값"] +    $this->page["표지_인쇄비"] +    $this->page["표지_판비"] +
            $this->page["표지_코팅비"] +    $this->page["표지_박비"] +    $this->page["표지_합지비"] +
            $this->page["표지_싸바리비"] + $this->page["표지_날개비"] +
            intval($this->page["표지_직접1_비용"]) +

            $this->page["삽지_종이값"] +    $this->page["삽지_인쇄비"] +    $this->page["삽지_판비"] +
            $this->page["내지_종이값"] +    $this->page["내지_인쇄비"] +    $this->page["내지_판비"] +

            $this->page["제본비"] +    $this->page["라운딩비"] +    $this->page["포장비"] +   $this->page["라벨작업비"] +
            $this->page["디자인비"] + $this->page["노임비"]  ;
        /*$this->page["디자인_표지"] +    $this->page["디자인_삽지"] +    $this->page["디자인_내지"] ;*/
        //$final = FeeCalculator::feeNote(); // + 제단비() + 제본비() + 포장비() + 배송비();
        $this->page['합계금액'] = $final;
    }

}
