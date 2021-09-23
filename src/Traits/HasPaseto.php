<?php

namespace RCerljenko\LaravelJwt\Traits;

use RCerljenko\LaravelPaseto\Paseto;

trait HasPaseto
{
	public function getJwtId()
	{
		return $this->getKey();
	}

	public function getJwtValidFromTime()
	{
	}

	public function getJwtValidUntilTime()
	{
		$expiration = config('paseto.expiration');

		return $expiration ? now()->addMinutes($expiration) : null;
	}

	public function getJwtCustomClaims()
	{
		return [];
	}

	public function token(array $config = [])
	{
		$paseto = new Paseto;

		return $paseto->encodeToken($this, $config);
	}
}
