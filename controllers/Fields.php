<?php namespace ProFixS\Forms\Controllers;

use Request;
use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;
/**
 * Fields Back-end Controller
 */
class Fields extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.ReorderController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public $requiredPermissions = ['profixs.forms.manage_forms'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('ProFixS.Forms', 'forms');
    }

    public function reorderExtendQuery($query) {
        return ($form_id = Request::get('form_id'))
            ? $query->where('form_id', $form_id)
            : $query;
    }
}
