<?php namespace Cooka\Order;

/**
 * The plugin.php file (called the plugin initialization script) defines the plugin information class.
 */

use Backend;
use Illuminate\Support\Facades\Session;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function pluginDetails()
    {
        return [
            'name'        => 'Cooka Order',
            'description' => 'Make a order sheet, bills',
            'author'      => 'Kenny Heisher',
            'icon'        => 'icon-leaf'
        ];
    }

    public function registerComponents()
    {
        return [
            '\Cooka\Order\Components\Bill' => 'Bill',
            '\Cooka\Order\Components\Orders' => 'Orders',
            '\Cooka\Order\Components\OrderDetail' => 'OrderDetail',
            '\Cooka\Order\Components\OrderManage' => 'OrderManage',
            '\Cooka\Order\Components\Notecooker' => 'Notecooker'
        ];
    }
    public function registerReportWidgets()
    {
        return [
            'RainLab\GoogleAnalytics\ReportWidgets\TrafficOverview' => [
                'label'   => 'Google Analytics traffic overview',
                'context' => 'dashboard'
            ],
            'RainLab\GoogleAnalytics\ReportWidgets\TrafficSources' => [
                'label'   => 'Google Analytics traffic sources',
                'context' => 'dashboard'
            ]
        ];
    }
    public function registerMailTemplates()
    {
        return [
            'cooka.order::mail.ordered'    => '주문완료 후 이메일 발송함.',

        ];
    }
    public function registerNavigation()
    {
        return [
            'Bill' => [
                'label'       => 'Order',
                'url'         => Backend::url('cooka/order'),
                'icon'        => 'icon-pencil',
                'permissions' => ['cooka.order.*'],
                'order'       => 500,
            ]
        ];
    }

    /* sk modi skhekper / twig extension 추가하기 ========================================= */

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'hide_kname' => [$this, 'hide_kname'],
                'tel_html' => [$this, 'tel_html'],
                'day_han' => [$this, 'day_han'],
                //'plural' => 'str_plural',
            ],
            'functions' => [
                // A static method call, i.e Form::open()
                //'form_open' => ['October\Rain\Html\Form', 'open'],

                'helloWorld' => function() { return 'Hello World!'; },
                'session' => function( $val ){
                    return Session::get($val);
                }
            ]
        ];
    }

    public function day_han( $str ) //
    {
        $daily = array('일','월','화','수','목','금','토');
        $date_number = date( "w", strtotime($str) ); //0 ~ 6 숫자 반환
        return $daily[ $date_number ];
    }
    public function tel_html($str) //
    {
        if( preg_match("/^[0-9- ]+$/D", $str)){
            $str = preg_replace("/[^0-9]*/s", "", $str);
            $len_hp = strlen($str);
            if( $len_hp < 9 )
                return preg_replace("/([0-9]+)([0-9]{4})/", "$1-$2", $str); //1661-5521
            //else if($len_hp > 14) 국제전화
            else return preg_replace("/(^02.{0}|^01.{1}|[0-9]{3})([0-9]+)([0-9]{4})/", "$1-$2-$3", $str);
        }else return $str;
    }

    public function hide_kname($str, $tail="*") //
    {
        $len=mb_strlen($str, 'UTF-8');
        if($len==0) {
            return '';
        }else if($len>2){
            return mb_substr($str,0,1, 'UTF-8').str_repeat($tail,$len-2).mb_substr($str,-1,1, 'UTF-8');
        }else
            return mb_substr($str,0,1, 'UTF-8').str_repeat($tail,$len-1);

    }
}
