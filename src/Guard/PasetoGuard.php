<?php

namespace RCerljenko\LaravelPaseto\Guard;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use RCerljenko\LaravelPaseto\Paseto;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class PasetoGuard implements Guard
{
	use GuardHelpers;

	public function __construct(UserProvider $provider)
	{
		$this->setProvider($provider);
	}

	/**
	 * Get the currently authenticated user.
	 */
	public function user(): ?Authenticatable
	{
		if ($this->hasUser() && !app()->runningUnitTests()) {
			return $this->user;
		}

		$decoded = $this->getTokenPayload();

		if (!$decoded) {
			return null;
		}

		$this->user = $this->getProvider()->retrieveById($decoded['jti']);

		return $this->user;
	}

	/**
	 * Validate a user's credentials.
	 */
	public function validate(array $credentials = []): bool
	{
		return !empty($this->attempt($credentials));
	}

	public function attempt(array $credentials = []): ?Authenticatable
	{
		$provider = $this->getProvider();

		$this->user = $provider->retrieveByCredentials($credentials);
		$this->user = $this->user && $provider->validateCredentials($this->user, $credentials) ? $this->user : null;

		return $this->user;
	}

	public function getTokenPayload(): ?array
	{
		$token = $this->getTokenFromRequest();

		if (!$token) {
			return null;
		}

		$paseto = new Paseto;

		return $paseto->decodeToken($token)->getClaims();
	}

	private function getTokenFromRequest(): ?string
	{
		$request = request();

		return $request->bearerToken() ?? $request->token;
	}
}
