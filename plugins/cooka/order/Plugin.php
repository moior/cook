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
            '\Cooka\Order\Components\Orders' => 'Orders',
            '\Cooka\Order\Components\OrderDetail' => 'OrderDetail',
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
}
