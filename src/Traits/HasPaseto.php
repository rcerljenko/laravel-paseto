<?php

namespace RCerljenko\LaravelPaseto\Traits;

use Illuminate\Support\Carbon;
use RCerljenko\LaravelPaseto\Paseto;

trait HasPaseto
{
	public function getJwtId(): string
	{
		return $this->getKey();
	}

	public function getJwtValidFromTime(): ?Carbon
	{
		return null;
	}

	public function getJwtValidUntilTime(): ?Carbon
	{
		$expiration = config('paseto.expiration');

		return $expiration ? now()->addMinutes($expiration) : null;
	}

	public function getJwtCustomClaims(): array
	{
		return [];
	}

	public function token(array $config = []): string
	{
		$paseto = new Paseto;

		return $paseto->encodeToken($this, $config);
	}
}
