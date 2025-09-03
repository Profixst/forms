<?php namespace ProFixS\Forms\Models;

use Config;
use Model;

/**
 * Field Model
 */
class Field extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sortable;

    public $table = 'profixs_forms_fields';

    public $rules = [
        'code' => 'required',
        'type' => 'required|in:text,textarea,select,selecttree,checkbox,multicheckbox,radio,recaptcha,label,file,phone',
        'file_extensions_list' => 'required_if:type,file|nullable'
    ];

    protected $guarded = ['*'];
    protected $fillable = [];

    // Використовуємо тільки jsonable для уникнення конфлікту
    protected $jsonable = ['options', 'field_rules', 'rules_options', 'file_extensions_list'];

    protected $filtered_rules = [
        'limit' => ['text', 'textarea'],
        'required' => ['text', 'textarea', 'select', 'checkbox', 'multicheckbox', 'radio', 'file', 'phone']
    ];

    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function beforeValidate()
    {
        if ($this->file_extensions_list == 0) {
            unset($this->file_extensions_list);
        }
        $this->rules['file_extensions_list.*'] = 'in:' . implode(',', Config::get('profixs.forms::config.available_form_file_extensions'));
    }

    /**
     * getFileExtensionsAttribute
     */
    public function getFileExtensionsAttribute()
    {
        return implode(', ', $this->file_extensions_list ?? []);
    }

    /**
     * getValidateRulesAttribute
     */
    public function getValidateRulesAttribute()
    {
        $rules = is_array($this->field_rules)
            ? $this->field_rules
            : [];

        switch ($this->type) {
            case 'multicheckbox':
                $rules = array_merge($rules, ['array']);
                break;
            case 'file':
                $rules = array_merge($rules, ['file']);
                if ($this->file_extensions_list) {
                    $rules = array_merge($rules, ['mimes:' . implode(',', $this->file_extensions_list)]);
                }
                break;
            case 'recaptcha':
            case 'label':
                $rules = [];
                break;
        }

        return $rules;
    }

    /**
     * Filter executed on backend action dependsOn
     * @param $fields
     * @param null $context
     */
    public function filterFields($fields, $context = null)
    {
        foreach ($this->filtered_rules as $key => $item) {
            $fields = $this->filterValidatesFields($fields, $item, $key);
        }
    }

    /**
     * @param $fields
     * @param $types
     * @param $validator
     * @return mixed
     */
    protected function filterValidatesFields($fields, $types, $validator)
    {
        if (isset($fields->type) &&
            !in_array($fields->type->value, $types)) {
            if ($fields->field_rules->value) {
                $fields->field_rules->value = collect($fields->field_rules->value)
                    ->filter(function ($item) use ($validator) {
                        return !in_array($item, [$validator]);
                    })->toArray();
            }
        }

        $this->filterValidateOptionsFields($validator, $fields,
            !in_array($validator, is_array($fields->field_rules->value ?? null) ? $fields->field_rules->value : []));

        return $fields;
    }

    /**
     * @param $checked_field
     * @param $fields
     * @return mixed
     */
    protected function filterValidateOptionsFields($checked_field, $fields, $hidden = true)
    {
        foreach ($fields as $key => $item) {
            if (isset($item->trigger['field']) &&
                strpos($item->trigger['field'], "field_rules[]") !== false &&
                strpos($item->trigger['condition'], "[$checked_field]") !== false
            ) {
                if($hidden) {
                    $fields->{$key}->value = '';
                }
                $fields->{$key}->hidden = $hidden;
            }
        }

        return $fields;
    }

    /**
     * getFileExtensionsListOptions
     */
    public function getFileExtensionsListOptions()
    {
        $extensions = Config::get('profixs.forms::config.available_form_file_extensions');
        return array_combine($extensions, $extensions);
    }
}
