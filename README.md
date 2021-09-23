# Laravel PASETO

Simple PASETO Auth for Laravel PHP Framework using [paragonie/paseto](https://github.com/paragonie/paseto) under the hood.

## Installation

Standard [Composer](https://getcomposer.org/download) package installation:

```sh
composer require rcerljenko/laravel-paseto -v
```

## Usage

1. Publish the config file. This will create a `config/paseto.php` file for basic configuration options.

```sh
php artisan vendor:publish --provider="RCerljenko\LaravelPaseto\LaravelPasetoServiceProvider" --tag="config"
```

2. Add a new auth guard to your auth config file using a `paseto` driver.

```php
// config/auth.php

'guards' => [
	'web' => [
		'driver' => 'session',
		'provider' => 'users',
	],

	'api' => [
		'driver' => 'paseto',
		'provider' => 'users',
	],
],
```

3. Protect your API routes using this new guard.

```php
// routes/api.php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
	// PASETO protected routes
});
```

4. Use provided `HasPaseto` trait from this package on your Auth model (eg. User).

```php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use RCerljenko\LaravelPaseto\Traits\HasPaseto;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
	use Notifiable, HasPaseto;
}
```

You now have access to `token()` method on your User model, eg:

```php
$user = User::findOrFail(1);
$user->token();
```

You should probably return this token via Login Controller or User Resource.

## Configuration

This package provides simple configuration via `config/paseto.php` file after you publish the config. Let's go over each configuration option.

- `secret-key` - Secret key to use when encoding / decoding tokens. It has to be a 32 byte long random string. Remember, if you change this key all active PASETO tokens will be invalidated.
- `expiration` - Default token expiration time in minutes. You can set it to `null` and the tokens will never expire.
- `claims` - Default claims that will be applied to all tokens (besides the required ones needed for decoding and validation).

This was global configuration for all tokens. Besides that, library provides a local per-model configuration via `HasPaseto` trait helper methods.

- `getJwtId()` - It should return the model unique key used to retrieve that model from database. It defaults to model primary key.
- `getJwtValidFromTime()` - It should return `null` (default) or a Carbon instance. You can use that if you want to create tokens which are not active right away.
- `getJwtValidUntilTime()` - It should return `null` or a Carbon instance. This sets the JWT expiration time which, by default, uses the `expiration` option from the config file.
- `getJwtCustomClaims()` - Should return a key/value array of extra custom claims that you want to be a part of your token. By default it's an empty array.

You can also use configuration directly on the `token()` method which then overrides all other configurations, eg:

```php
$user->token([
	'id' => $user->email,
	'valid_from' => now()->addHour(),
	'valid_until' => now()->addDay(),
	'claims' => [
		'extra1' => 'foo',
		'extra2' => 'bar'
	]
]);
```

You don't need to override all configuration options, just the ones that you wish to change.

## Request

Token is extracted from the request in one of three ways:

1. From `Authorization: Bearer {token}` header (most common).
2. From URL query param `token`.
3. From request payload using `token` field name.
