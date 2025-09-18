<?php namespace ProFixS\Forms;

use Backend;
use ProFixS\Forms\Models\Inbox;
use System\Classes\PluginBase;
use Validator;

/**
 * Forms Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'profixs.forms::lang.plugin.name',
            'description' => 'profixs.forms::lang.plugin.description',
            'author'      => 'ProFixS',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
       /**   Validator::replacer('recaptcha', 'ProFixS\Forms\Validators\Recaptcha@recaptchaMessage');
       * Validator::extend('recaptcha', 'ProFixS\Forms\Validators\Recaptcha@recaptcha'); */
        Validator::extend('recaptcha', [\ProFixS\Forms\Validators\Recaptcha::class, 'recaptcha']);
        Validator::replacer('recaptcha', [\ProFixS\Forms\Validators\Recaptcha::class, 'recaptchaMessage']);

        Validator::replacer('auth', 'ProFixS\Forms\Validators\Auth@message');
        Validator::extend('auth', 'ProFixS\Forms\Validators\Auth@validate');
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'ProFixS\Forms\Components\Form' => 'form',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'profixs.forms.manage_forms' => [
                'tab'   => 'profixs.forms::lang.permissions.tabs.forms',
                'label' => 'profixs.forms::lang.permissions.permissions.manage_forms'
            ],
            'profixs.forms.manage_inbox' => [
                'tab'   => 'profixs.forms::lang.permissions.tabs.forms',
                'label' => 'profixs.forms::lang.permissions.permissions.manage_inbox'
            ],
            'profixs.forms.access_settings' => [
                'tab'   => 'profixs.forms::lang.permissions.tabs.forms',
                'label' => 'profixs.forms::lang.permissions.permissions.access_settings'
            ]
        ];
    }

    /**
     * registerSettings
     */
    public function registerSettings()
    {
        return [
            'inbox' => [
                'label'       => 'profixs.forms::lang.settings_menu.menu.inbox',
                'description' => '',
                'icon'        => 'icon-envelope',
                'url'         => Backend::url('profixs/forms/inboxes'),
                'order'       => 1,
                'category'    => 'profixs.forms::lang.settings_menu.tabs.forms',
                'permissions' => ['profixs.forms.manage_inbox'],
                'counter'     => Inbox::getUnreadedInboxCount()
            ],
            'forms' => [
                'label'       => 'profixs.forms::lang.settings_menu.menu.forms',
                'description' => '',
                'icon'        => 'icon-list-ol',
                'url'         => Backend::url('profixs/forms/forms'),
                'order'       => 2,
                'category'    => 'profixs.forms::lang.settings_menu.tabs.forms',
                'permissions' => ['profixs.forms.manage_forms']
            ],
            'settings' => [
                'label'       => 'profixs.forms::lang.settings_menu.menu.settings',
                'description' => '',
                'icon'        => 'icon-cogs',
                'class'       => 'ProFixS\Forms\Models\Settings',
                'category'    => 'profixs.forms::lang.settings_menu.tabs.forms',
                'order'       => 3,
                'permissions' => ['profixs.forms.access_settings'],
            ]
        ];
    }

    /**
     * registerReportWidgets
     */
    public function registerReportWidgets()
    {
        return [
            'ProFixS\Forms\ReportWidgets\UnreadedInboxes' => [
                'label'   => 'profixs.forms::lang.widgets.unreaded_inboxes.name',
                'context' => 'dashboard'
            ]
        ];
    }
}
