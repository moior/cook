<?php namespace Cooka\Order;

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
            '\Cooka\Order\Components\Orders' => 'Orders'
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
            'Bill' => [
                'label'       => 'Order',
                'url'         => Backend::url('cooka/order'),
                'icon'        => 'icon-pencil',
                'permissions' => ['cooka.order.*'],
                'order'       => 500,
            ]
        ];
    }
}
