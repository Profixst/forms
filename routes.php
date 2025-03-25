<?php

use ProFixS\Forms\Classes\Auth;

Route::middleware('web')->get('auth/kyivid', function () {
	$data = request()->all();

    $rules = [
        'redirect_url' => 'required|url',
    ];

    $validator = Validator::make($data, $rules);

    if ($validator->fails()) {
        throw new ValidationException($validator);
    }

    try {
		Auth::setRedirectUrl($data['redirect_url']);

		$url = Auth::instance()->getKyividAuthUrl();
	} catch (Exception $e) {
		trace_log($e);
		return redirect('/');
	}

	return redirect($url);
});

Route::middleware('web')->get('auth/kyivid/callback', function () {
	$data = request()->all();

    $rules = [
        'code' => 'required|string',
    ];

    $validator = Validator::make($data, $rules);

    if ($validator->fails()) {
        throw new ValidationException($validator);
    }

    try {
		Auth::instance()->auth($data['code']);

		$url = Auth::getRedirectUrl();
	} catch (Exception $e) {
		trace_log($e);
		return redirect('/');
	}

	return redirect($url);
});

Route::middleware('web')->post('logout', function () {
	return Auth::logout();
});
