<?php namespace Boonzero\Sms;

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
            'name'        => 'Sms',
            'description' => 'Make a Sms',
            'author'      => 'Kenny Heisher',
            'icon'        => 'icon-leaf'
        ];
    }

    public function registerComponents()
    {
        return [
            '\Boonzero\Sms\Components\SmsControl' => 'Sms',
            '\Boonzero\Sms\Components\SmsPhoneControl' => 'SmsPhone',
            '\Boonzero\Sms\Components\SmsHistoryControl' => 'SmsHistory',
            '\Boonzero\Sms\Components\SmsContentControl' => 'SmsContent',
        ];
    }
    public function registerReportWidgets()
    {
        return [];
    }
    public function registerMailTemplates()
    {
        return [];
    }
    public function registerNavigation()
    {
        return [];
    }
}
