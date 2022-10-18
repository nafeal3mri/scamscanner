<?php

return array(
    /*
	|--------------------------------------------------------------------------
	| One Signal App Id
	|--------------------------------------------------------------------------
	|
	|
	*/
    'app_id' => env('ONESIGNAL_APP_ID'),

    /*
	|--------------------------------------------------------------------------
	| Rest API Key
	|--------------------------------------------------------------------------
	|
    |
	|
	*/
    'rest_api_key' => env('ONESIGNAL_REST_API_KEY'),
    'user_auth_key' => env('USER_AUTH_KEY'),

    /*
	|--------------------------------------------------------------------------
	| Guzzle Timeout
	|--------------------------------------------------------------------------
	|
    |
	|
	*/
    'guzzle_client_timeout' => env('ONESIGNAL_GUZZLE_CLIENT_TIMEOUT', 0),

	'dashboard_app_id' => env('ADMIN_ONESIGNAL_APP_ID'),
	'dashboard_API_key' => env('ADMIN_ONESIGNAL_REST_API_KEY'),
);
