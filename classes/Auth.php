<?php namespace ProFixS\Forms\Classes;

use Crypt;
use Exception;
use GuzzleHttp\Client;
use ProFixS\Forms\Classes\Interfaces\AuthInterface;
use ProFixS\Forms\Models\Settings;
use Session;
use ValidationException;

class Auth implements AuthInterface
{
	use \October\Rain\Support\Traits\Singleton;

	const SESSION_USER_KEY = 'kyivid_user';
	const SESSION_REDIRECT_URL = 'kyivid_redirect_url';

    protected $client;
    protected $settings;

    /**
     * init
     */
    public function init()
    {
    	$this->settings = Settings::instance();

        $this->client = new Client([
            'base_uri' => $this->settings->auth_url,
            'timeout' => 10
        ]);
    }

    /**
     * getAuthCallbackUrl
     */
    protected function getAuthCallbackUrl()
    {
    	return url('auth/kyivid/callback');
    }

    /**
     * getAuthUrl
     */
    public function getAuthUrl(string $redirectUrl): string
    {
    	return url('auth/kyivid') . '?redirect_url=' . $redirectUrl;
    }

    /**
     * getKyividAuthUrl
     */
    public function getKyividAuthUrl(): string
    {
    	return sprintf('%s/authorize?response_type=%s&client_id=%s&redirect_uri=%s&scope=openid+profile+address+email', 
            $this->settings->auth_url, 'code', $this->settings->auth_client_id, $this->getAuthCallbackUrl()
        );
    }

    /**
     * setRedirectUrl
     */
    public static function setRedirectUrl(string $url): void
    {
    	Session::put(self::SESSION_REDIRECT_URL, $url);
    }

    /**
     * getRedirectUrl
     */
    public static function getRedirectUrl()
    {
    	return Session::get(self::SESSION_REDIRECT_URL);
    }

    /**
     * check
     */
    public static function check(): bool
    {
    	return Session::has(self::SESSION_USER_KEY);
    }

    /**
     * getUser
     */
    public static function getUser()
    {
    	return Crypt::decrypt(Session::get(self::SESSION_USER_KEY));
    }

    /**
     * logout
     */
    public static function logout()
    {
    	return Session::flash(self::SESSION_USER_KEY);
    }

    /**
     * auth
     */
    public function auth(string $code): array
    {
    	if (!$token = $this->getAccessToken($code, $this->getAuthCallbackUrl())) {
    		throw new ValidationException(['code' => 'Invalid Code']);
    	}

    	if (!$user = $this->getUserinfo($token)) {
    		throw new Exception('Get userinfo error');
    	}

    	Session::put(self::SESSION_USER_KEY, Crypt::encrypt($user));

    	return $user;
    }

	/**
	 * getAccessToken
	 */
	protected function getAccessToken(string $authorizationCode)
	{
		try {
			$response = $this->client->request('POST', '/token', [
                'query' => [
                    'grant_type' => 'authorization_code',
                    'code' => $authorizationCode,
                    'redirect_uri' => $this->getAuthCallbackUrl()
                ],
	                'headers' => [
                	'Authorization' => sprintf('Basic %s', base64_encode($this->settings->auth_client_id . ':' . $this->settings->auth_client_secret)),
                	'content-type' => 'application/x-www-form-urlencoded'
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!$token = array_get($data, 'access_token')) {
            	throw new Exception('access_token not found in response: ' . json_encode($data, JSON_UNESCAPED_UNICODE) . ', statusCode: ' . $response->getStatusCode() . ', reasonPhrase: ' . $response->getReasonPhrase());
            }
        } catch (Exception $e) {
        	trace_log($e);
        	return;
        }

      	return $token;
	}

	/**
	 * getUserinfo
	 */
	protected function getUserinfo(string $token)
	{
		try {
			$response = $this->client->request('GET', '/userinfo', [
	            'headers' => [
	            	'Authorization' => sprintf('Bearer %s', $token),
	            ]
	        ])->getBody()->getContents();

	    	$data = json_decode($response, true);

	    	$result = [
				'email' => array_get($data, 'email'),
				'gender' => array_get($data, 'gender'),
				'address' => array_get($data, 'address'),
				'firstname' => array_get($data, 'given_name'),
				'lastname' => array_get($data, 'family_name'),
				'middlename' => array_get($data, 'middle_name'),
				'birthdate' => array_get($data, 'birthdate')
		    ];
        } catch (Exception $e) {
        	trace_log($e);
        	return;
        }

        return $result;
	}
}

