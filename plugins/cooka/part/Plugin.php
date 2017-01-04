<?php namespace Cooka\Part;

/**
 * The plugin.php file (called the plugin initialization script) defines the plugin information class.
 */

use Backend;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function pluginDetails()
    {
        return [
            'name'        => 'Cooka Sample',
            'description' => 'Parts',
            'author'      => 'Kenny Heisher',
            'icon'        => 'icon-leaf'
        ];
    }

    public function registerComponents()
    {
        return [
            '\Cooka\Sample\Components\PartControl' => 'PartControl',
        ];
    }
    /*'\Cooka\Sample\Components\SampleControl' => 'SampleControl',
            '\Cooka\Sample\Components\SampleList' => 'SampleList',
            '\Cooka\Sample\Components\SampleMenu' => 'SampleMenu',
*/
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
            'rainlab.user::mail.activate' => 'Activation mail sent to new users.',
            'rainlab.user::mail.restore'  => 'Password reset instructions for front-end users.'
        ];
    }

    /*이건 뭐지?? 장미라로 로그인하면 url로 리다이렉트됨.... 모두 삭제해볼까? 17.1.4.*/
    public function registerNavigation()
    {
        return [];
    }


    public function registerPermissions()
    {
        return [
            'cooka.part.update_part' => [
                'tab' => 'Cooka',
                'label' => '샘플 이미지와 설명을 저장, 수정할 권한',
               'order' => 10,
            ],
        ];
    }
}
