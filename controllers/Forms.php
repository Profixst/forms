<?php namespace ProFixS\Forms\Controllers;

use Config;
use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;

/**
 * Forms Back-end Controller
 */
class Forms extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController',
        '@ProFixS.MultiLanguage.Behaviors.MultiLanguageController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';
    public $bodyClass = 'compact-container';

    public $requiredPermissions = ['profixs.forms.manage_forms'];

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
