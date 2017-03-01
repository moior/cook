<?php namespace Cooka\Invoice;

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
            'name'        => 'Invoice',
            'description' => 'Make a Invoice',
            'author'      => 'Kenny Heisher',
            'icon'        => 'icon-leaf'
        ];
    }

    public function registerComponents()
    {
        return [
            '\Cooka\Invoice\Components\Invoices' => 'Invoices',
            '\Cooka\Invoice\Components\InvoiceManager' => 'InvoiceManager',
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
