<?php namespace ProFixS\Forms\Validators;

class Auth
{
	/**
     * recaptcha
     */
    public function validate($attribute, $value): boolean
    {
        return isValidAuthKey($value);
    }

    /**
     * Error message
     */
    public function recaptchaMessage($message, $attribute, $rule, $parameters)
    {
    	return 'Invalid auth key.';
    }
}

