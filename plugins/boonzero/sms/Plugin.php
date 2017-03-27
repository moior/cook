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
            '\Boonzero\Sms\Components\Sms' => 'Sms',
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
