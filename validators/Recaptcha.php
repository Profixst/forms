<?php namespace ProFixS\Forms\Validators;

use Request;
use ReCaptcha\ReCaptcha as GoogleRecaptcha;
use ProFixS\Forms\Models\Settings;
use Log;

class Recaptcha
{
    /**
     * Валідація reCAPTCHA токена
     */
    public function recaptcha($attribute, $value)
    {
        $secretKey = Settings::get('secret_key');

        if (empty($secretKey)) {
            Log::warning('reCAPTCHA validation skipped: secret key is missing.');
            return false;
        }

        $recaptcha = new GoogleRecaptcha($secretKey);

        $response = $recaptcha->verify($value, Request::ip());

        if (!$response->isSuccess()) {
            Log::warning('reCAPTCHA failed', [
                'errors' => $response->getErrorCodes()
            ]);
            return false;
        }

        $score = $response->getScore();
        $threshold = Settings::get('score_threshold', 0.5);

        if ($score < $threshold) {
            Log::info('reCAPTCHA score too low', [
                'score' => $score,
                'threshold' => $threshold
            ]);
            return false;
        }

        return true;
    }

    /**
     * Повідомлення про помилку
     */
    public function recaptchaMessage($message, $attribute, $rule, $parameters)
    {
        return 'Перевірка reCAPTCHA не пройдена.';
    }
}

