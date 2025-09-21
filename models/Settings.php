<?php namespace ProFixS\Forms\Models;

use Config;
use Model;

/**
 * Settings Model
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
        'auth_client_secret',
        'secret_key'
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

        if (!Config::get('profixs.forms::config.is_recaptcha_enabled')) {
            unset(
                $fields->tabs['fields']['site_key'],
                $fields->tabs['fields']['secret_key'],
                $fields->tabs['fields']['score_threshold']
            );
        }

        return $fields;
    }

    /**
     * formExtendFields
     */
    public function formExtendFields($form, $fields)
    {
        // Можна додати динамічну логіку для полів, якщо потрібно
    }
}
