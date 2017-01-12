<?php namespace Kenny\Sms;

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
            'name'        => 'Kenny',
            'description' => 'Kenny Own Plugin',
            'author'      => 'Kenny Heisher',
            'icon'        => 'icon-leaf'
        ];
    }

    public function registerComponents()
    {
        return [
            '\Kenny\Sms\Components\SmsController' => 'SMS',

        ];
    }
    public function registerReportWidgets()
    {
        return [];
    }
    public function registerMailTemplates()
    {
        return [
            'rainlab.user::mail.activate' => 'Activation mail sent to new users.',
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
            'kenny.sms.send' => [
                'tab' => 'SMS',
                'label' => '문자 발송 권한',
               'order' => 10,
            ],
        ];
    }
}
