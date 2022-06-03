<?php

namespace RCerljenko\LaravelPaseto;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use RCerljenko\LaravelPaseto\Guard\PasetoGuard;

class LaravelPasetoServiceProvider extends ServiceProvider
{
	public function boot(): void
	{
		$this->publishConfig();

		$this->extendAuthGuard();
	}

	public function register(): void
	{
		$this->mergeConfigFrom(__DIR__ . '/../config/paseto.php', 'paseto');
	}

	private function publishConfig(): void
	{
		$this->publishes([
			__DIR__ . '/../config/paseto.php' => config_path('paseto.php'),
		], 'config');
	}

	private function extendAuthGuard(): void
	{
		auth()->extend('paseto', function (Application $app, string $name, array $config): PasetoGuard {
			return new PasetoGuard(auth()->createUserProvider($config['provider']));
		});
	}
}
