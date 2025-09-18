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
