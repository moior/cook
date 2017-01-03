<?php namespace Cooka\Sample;

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
            'description' => 'Sample',
            'author'      => 'Kenny Heisher',
            'icon'        => 'icon-leaf'
        ];
    }

    public function registerComponents()
    {
        return [
            '\Cooka\Sample\Components\SampleControl' => 'SampleControl',
            '\Cooka\Sample\Components\SampleList' => 'SampleList',
            '\Cooka\Sample\Components\SampleMenu' => 'SampleMenu',
            '\Cooka\Sample\Components\CategoryByFilename' => 'CategoryByFilename',

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
            'rainlab.user::mail.activate' => 'Activation mail sent to new users.',
            'rainlab.user::mail.restore'  => 'Password reset instructions for front-end users.'
        ];
    }
    public function registerNavigation()
    {
        return [
            'Sample' => [
                'label'       => 'Sample',
                'url'         => Backend::url(''),
                'icon'        => 'icon-pencil',

                'order'       => 500,
            ]/*'permissions' => ['cooka.sample.*'],*/
        ];
    }


    public function registerPermissions()
    {
        return [
            'cooka.sample.update_sample' => [
                'tab' => 'Cooka',
                'label' => '샘플 이미지와 설명을 저장, 수정할 권한',
               'order' => 10,
            ],
        ];
    }
}
