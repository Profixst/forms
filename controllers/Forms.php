<?php namespace ProFixS\Forms\Controllers;

use Config;
use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;

/**
 * Forms Backend Controller
 *
 * @link https://docs.octobercms.com/3.x/extend/system/controllers.html
 */
class Forms extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController',
        '@ProFixS.MultiLanguage.Behaviors.MultiLanguageController'
    ];

    /**
     * @var string formConfig file
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string listConfig file
     */
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';
    /**
     * @var array required permissions
     */
    public $bodyClass = 'compact-container';

    public $requiredPermissions = ['profixs.forms.manage_forms'];

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('ProFixS.Forms', 'forms');
    }

    public function formExtendFields($form, $fields)
    {
        if (!Config::get('profixs.forms::config.is_authorization_available')) {
            $form->removeField('is_auth_required');
        }
    }
}

