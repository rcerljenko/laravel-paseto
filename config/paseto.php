<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Secret key
	|--------------------------------------------------------------------------
	|
	| Used for token encoding/decoding.
	| Has to be a 32 byte long random string.
	|
	 */

	'secret-key' => env('PASETO_SECRET_KEY'),

	/*
	|--------------------------------------------------------------------------
	| Token expiration (in minutes)
	|--------------------------------------------------------------------------
	|
	| Token expiration time defined in minutes. If null then token has no expiration at all (lifetime token).
	|
	 */

	'expiration' => env('PASETO_EXPIRATION', 60 * 24),

	/*
	|--------------------------------------------------------------------------
	| Issuer
	|--------------------------------------------------------------------------
	|
	| Sets the token issuer claim.
	|
	 */

	'issuer' => env('APP_URL'),

	/*
	|--------------------------------------------------------------------------
	| Audience
	|--------------------------------------------------------------------------
	|
	| Sets the token audience claim.
	|
	 */

	'audience' => env('APP_URL'),

	/*
	|--------------------------------------------------------------------------
	| Default claims
	|--------------------------------------------------------------------------
	|
	| These claims will be in all tokens.
	|
	 */

	'claims' => [
		// 'foo' => 'bar'
	],
];
