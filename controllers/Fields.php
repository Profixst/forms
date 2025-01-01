<?php namespace ProFixS\Forms\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Request;
use System\Classes\SettingsManager;
/**
 * Fields Backend Controller
 *
 * @link https://docs.octobercms.com/3.x/extend/system/controllers.html
 */
class Fields extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.ReorderController'
    ];

    /**
     * @var string formConfig file
     */
    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    /**
     * @var array required permissions
     */
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

    public function reorderExtendQuery($query) {
        return ($form_id = Request::get('form_id'))
            ? $query->where('form_id', $form_id)
            : $query;
    }
}
