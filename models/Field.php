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
    protected $jsonable = [
        'options',
        'field_rules',
        'rules_options',
        'file_extensions_list'
    ];

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
        if (empty($this->file_extensions_list) || $this->file_extensions_list === 0) {
            unset($this->file_extensions_list);
        }

        $extensions = Config::get('profixs.forms::config.available_form_file_extensions');
        $this->rules['file_extensions_list.*'] = 'in:' . implode(',', (array) $extensions);
    }

    public function getFileExtensionsAttribute()
    {
        return implode(', ', (array) $this->file_extensions_list);
    }

    public function getValidateRulesAttribute()
    {
        $rules = is_array($this->field_rules) ? $this->field_rules : [];

        switch ($this->type) {
            case 'multicheckbox':
                $rules[] = 'array';
                break;
            case 'file':
                $rules[] = 'file';
                if (!empty($this->file_extensions_list)) {
                    $rules[] = 'mimes:' . implode(',', (array) $this->file_extensions_list);
                }
                break;
            case 'recaptcha':
            case 'label':
                $rules = [];
                break;
        }

        return $rules;
    }

    public function filterFields($fields, $context = null)
    {
        foreach ($this->filtered_rules as $key => $item) {
            $fields = $this->filterValidatesFields($fields, $item, $key);
        }
    }

    protected function filterValidatesFields($fields, $types, $validator)
    {
        if (isset($fields->type) && !in_array($fields->type->value, $types)) {
            if ($fields->field_rules->value) {
                $fields->field_rules->value = collect($fields->field_rules->value)
                    ->filter(fn($item) => $item !== $validator)
                    ->toArray();
            }
        }

        $activeRules = is_array($fields->field_rules->value ?? null) ? $fields->field_rules->value : [];
        $shouldHide = !in_array($validator, $activeRules);

        return $this->filterValidateOptionsFields($validator, $fields, $shouldHide);
    }

    protected function filterValidateOptionsFields($checked_field, $fields, $hidden = true)
    {
        foreach ($fields as $key => $item) {
            if (
                isset($item->trigger['field']) &&
                strpos($item->trigger['field'], "field_rules[]") !== false &&
                strpos($item->trigger['condition'], "[$checked_field]") !== false
            ) {
                if ($hidden) {
                    $fields->{$key}->value = '';
                }
                $fields->{$key}->hidden = $hidden;
            }
        }

        return $fields;
    }

    public function getFileExtensionsListOptions()
    {
        $extensions = Config::get('profixs.forms::config.available_form_file_extensions');
        return array_combine((array) $extensions, (array) $extensions);
    }
}
