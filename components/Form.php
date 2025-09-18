<?php namespace ProFixS\Forms\Components;

use Request;
use Config;
use Cms\Classes\ComponentBase;
use Db;
use Event as SystemEvent;
use Exception;
use ProFixS\Forms\Classes\Auth;
use ProFixS\Forms\Classes\Exceptions\ValidatorBuilderException;
use ProFixS\Forms\Classes\ValidatorBuilderFactory;
use ProFixS\Forms\Models\Form as FormModel;
use ProFixS\Forms\Models\Inbox;
use ProFixS\Forms\Models\Settings;
use System\Models\File;
use ValidationException;
use Validator;
use Queue;
use Illuminate\Support\Facades\Http;

class Form extends ComponentBase
{
    use \System\Traits\ViewMaker;

    public $form;
    public $recaptcha;
    public $user;
    public $authUrl;

    public function componentDetails()
    {
        return [
            'name' => 'profixs.forms::lang.components.form.name',
            'description' => 'profixs.forms::lang.components.form.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'code' => [
                'title' => 'profixs.forms::lang.components.form.fields.code',
                'type' => 'dropdown',
                'options' => FormModel::get()->lists('code', 'code'),
                'emptyOption' => '-'
            ]
        ];
    }

    public function onRun()
    {
        $this->addJs('/plugins/profixs/forms/assets/js/forms-ajax.js?v=' . \System\Models\PluginVersion::getVersion('ProFixS.Forms'));
        $this->form = $this->loadForm();
        $this->recaptcha = $this->loadRecaptcha();
    }

    public function onRenderAuthForm()
    {
        try {
            if (Auth::check()) {
                $this->user = Auth::getUser();
            } elseif ($this->isAuthRequired()) {
                $this->authUrl = Auth::instance()->getAuthUrl(Request::url());
            }
        } catch (Exception $e) {
            trace_log($e);
            return response()->json('Something was wrong.', 500);
        }
    }

    protected function isAuthRequired(): bool
    {
        if (!Config::get('profixs.forms::config.is_authorization_available')) {
            return false;
        }

        return $this->form->is_auth_required;
    }

    protected function loadForm()
    {
        return FormModel::with('fields')
            ->where('code', $this->property('code'))
            ->first();
    }

    protected function loadRecaptcha()
    {
        return [
            'site_key' => Settings::get('site_key'),
            'lang' => Settings::get('lang')
        ];
    }

    public function onSubmitForm()
    {
        $this->validateFormId();
        $this->form = FormModel::find(request()->get('form_id'));
        $this->validateAuthorization();
        $this->validateFormFields();

        Db::beginTransaction();
        try {
            $inbox = Inbox::make();
            $inbox->form_id = request()->get('form_id');
            $inbox->fields = request()->only($this->form->fields->lists('code'));
            $inbox->ip = request()->ip();
            $inbox->save();

            foreach ($this->form->fields as $field) {
                if ($field->type !== 'file') {
                    continue;
                }

                if ($file = request()->file($field->code)) {
                    $_file = new File;
                    $_file->data = $file;
                    $_file->is_public = false;
                    $_file->save();
                    $inbox->files()->add($_file);
                }
            }
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }

        Db::commit();

        $data = ['inbox_id' => $inbox->id];
        SystemEvent::fire('profixs.forms::component.form.sendMail', [&$data]);

        if ($this->form->send && count($this->form->emails)) {
            Queue::push('ProFixS\Forms\Jobs\SendFormMail@fire', $data);
        }

        $this->vars['inbox'] = $inbox;
        SystemEvent::fire('profixs.forms::inbox.afterSave', $inbox);
    }

    protected function validateFormId()
    {
        $validator = Validator::make(
            request()->only(['form_id']),
            ['form_id' => 'required|integer|exists:profixs_forms_forms,id']
        );
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    protected function validateFormFields()
    {
        if (!$this->form) {
            throw new Exception('Form_id is not set.');
        }

        if (!count($this->form->fields)) {
            return;
        }

        $validate_messages = [];
        foreach ($this->form->fields as $field) {
            if (!$field->validateRules || $field->type == 'recaptcha') {
                continue;
            }

            $validate_rules = $field->validateRules;
            if (is_array($validate_rules)) {
                $validate_replaces = [];
                foreach ($validate_rules as $key => $item) {
                    $data = array_get($field->rules_options, $item);
                    try {
                        $validate_replaces = array_merge(
                            (new ValidatorBuilderFactory)->factory($item)->build($data),
                            $validate_replaces
                        );
                        $validate_messages = array_merge(
                            (new ValidatorBuilderFactory)->factory($item)->messages(),
                            $validate_messages
                        );
                        unset($validate_rules[$key]);
                    } catch (ValidatorBuilderException $e) {}
                }
                $validate_rules = array_merge($validate_rules, $validate_replaces);
            }
            $rules[$field->code] = $validate_rules;
        }

        $validator = Validator::make(
            request()->only($this->form->fields->lists('code')),
            $rules ?? [],
            $validate_messages,
            $this->form->fields->lists('title', 'code')
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        foreach ($this->form->fields as $field) {
            if ($field->type !== 'recaptcha') {
                continue;
            }

            $token = request()->input('g-recaptcha-response');
            if (!$token) {
                throw new ValidationException([
                    'g-recaptcha-response' => $field->title . ' is required.'
                ]);
            }

            $this->validateRecaptchaEnterprise($token);
        }
    }

    protected function validateRecaptchaEnterprise(string $token): void
    {
        $siteKey = Settings::get('site_key');
        $apiKey = Settings::get('api_key');

        $response = Http::post("https://recaptchaenterprise.googleapis.com/v1/projects/profix-brovary-r-1757840099873/assessments?key={$apiKey}", [
            'event' => [
                'token' => $token,
                'siteKey' => $siteKey,
                'expectedAction' => 'submit',
            ]
        ]);

        $result = $response->json();

        if (!($result['tokenProperties']['valid'] ?? false)) {
            throw new ValidationException([
                'recaptcha' => 'reCAPTCHA validation failed.'
            ]);
        }
    }

    protected function validateAuthorization()
    {
        if (!$this->isAuthRequired()) {
            return;
        }

        if (!Auth::check()) {
            throw new ValidationException([
                'auth' => 'Authorization is required.'
            ]);
        }
    }
}

