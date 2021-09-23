<?php

namespace RCerljenko\LaravelPaseto;

use DateTime;
use ParagonIE\Paseto\Parser;
use ParagonIE\Paseto\Builder;
use ParagonIE\Paseto\Purpose;
use ParagonIE\Paseto\Rules\ValidAt;
use ParagonIE\Paseto\Rules\IssuedBy;
use ParagonIE\Paseto\Rules\NotExpired;
use ParagonIE\Paseto\Keys\SymmetricKey;
use ParagonIE\Paseto\Protocol\Version4;
use ParagonIE\Paseto\Rules\ForAudience;
use ParagonIE\Paseto\ProtocolCollection;

class Paseto
{
	private $sharedKey;

	public function __construct(?string $secretKey = null)
	{
		$this->sharedKey = new SymmetricKey($secretKey ?? config('paseto.secret-key'));
	}

	public function encodeToken(object $user, array $config = [])
	{
		$builder = new Builder;

		return $builder
			->setKey($this->sharedKey)
			->setVersion(new Version4)
			->setPurpose(Purpose::local())
			->setIssuer(config('paseto.issuer'))
			->setAudience(config('paseto.audience'))
			->setIssuedAt()
			->setNotBefore(new DateTime($config['valid_from'] ?? $user->getJwtValidFromTime()))
			->setExpiration(new DateTime($config['valid_until'] ?? $user->getJwtValidUntilTime()))
			->setJti($config['id'] ?? $user->getJwtId())
			->setClaims($config['claims'] ?? $user->getJwtCustomClaims())
			->toString();
	}

	public function decodeToken(string $token)
	{
		$parser = new Parser;

		return $parser
			->setKey($this->sharedKey)
			->setAllowedVersions(ProtocolCollection::v4())
			->setPurpose(Purpose::local())
			->addRule(new IssuedBy(config('app.issuer')))
			->addRule(new ForAudience(config('app.audience')))
			->addRule(new ValidAt)
			->addRule(new NotExpired)
			->parse($token);
	}
}
