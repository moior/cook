<?php namespace Kenny\Memo;

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
            'name'        => 'Memo',
            'description' => 'Make a Memo',
            'author'      => 'Kenny Heisher',
            'icon'        => 'icon-leaf'
        ];
    }

    public function registerComponents()
    {
        return [
            '\Kenny\Memo\Components\Memos' => 'Memos',
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
