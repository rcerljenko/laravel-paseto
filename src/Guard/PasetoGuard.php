<?php

namespace RCerljenko\LaravelPaseto\Guard;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use RCerljenko\LaravelPaseto\Paseto;
use Illuminate\Contracts\Auth\UserProvider;

class PasetoGuard implements Guard
{
	use GuardHelpers;

	public function __construct(UserProvider $provider)
	{
		$this->setProvider($provider);
	}

	/**
	 * Get the currently authenticated user.
	 *
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function user()
	{
		if ($this->user && !app()->runningUnitTests()) {
			return $this->user;
		}

		$decoded = $this->getTokenPayload();

		if (!$decoded) {
			return;
		}

		$this->user = $this->getProvider()->retrieveById($decoded['jti']);

		return $this->user;
	}

	/**
	 * Validate a user's credentials.
	 *
	 * @return bool
	 */
	public function validate(array $credentials = [])
	{
		return !empty($this->attempt($credentials));
	}

	public function attempt(array $credentials = [])
	{
		$provider = $this->getProvider();

		$this->user = $provider->retrieveByCredentials($credentials);
		$this->user = $this->user && $provider->validateCredentials($this->user, $credentials) ? $this->user : null;

		return $this->user;
	}

	public function getTokenPayload()
	{
		$token = $this->getTokenFromRequest();

		if (!$token) {
			return;
		}

		$paseto = new Paseto;

		return $paseto->decodeToken($token)->getClaims();
	}

	private function getTokenFromRequest()
	{
		$request = request();

		return $request ? ($request->bearerToken() ?? $request->token) : null;
	}
}
