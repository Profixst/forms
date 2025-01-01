<?php namespace ProFixS\Forms\Models;

use Config;
use Model;

/**
 * Settings Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class Settings extends Model
{
    use \October\Rain\Database\Traits\Encryptable;

    public $implement = [
    	'@ProFixS.MultiLanguage.Behaviors.MultiLanguageSettingsModel',
    	'@ProFixS.MultiSite.Behaviors.MultiSiteSettingsModel',
    	'@ProFixS.Revisions.Behaviors.RevisionsModel',
        'System.Behaviors.SettingsModel'
    ];

    public $settingsCode = 'profixs_forms_settings';

    public $settingsFields = 'fields.yaml';

    /**
     * @var array List of attributes to encrypt.
     */
    protected $encryptable = [
        'auth_client_id',
        'auth_client_secret'
    ];

    /**
     * getFieldConfig
     */
    public function getFieldConfig()
    {
        $fields = $this->makeConfig($this->settingsFields);

        if (!Config::get('profixs.forms::config.is_authorization_available')) {
            unset(
                $fields->tabs['fields']['auth_url'],
                $fields->tabs['fields']['auth_client_id'],
                $fields->tabs['fields']['auth_client_secret']
            );
        }

        return $fields;
    }

    /**
     * formExtendFields
     */
    public function formExtendFields($form, $fields)
    {

    }
}

