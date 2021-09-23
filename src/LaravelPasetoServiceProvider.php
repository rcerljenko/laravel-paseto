<?php

namespace RCerljenko\LaravelPaseto;

use Illuminate\Support\ServiceProvider;
use RCerljenko\LaravelPaseto\Guard\PasetoGuard;

class LaravelPasetoServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->publishConfig();

		$this->extendAuthGuard();
	}

	public function register()
	{
		$this->mergeConfigFrom(__DIR__ . '/../config/paseto.php', 'paseto');
	}

	private function publishConfig()
	{
		$this->publishes([
			__DIR__ . '/../config/paseto.php' => config_path('paseto.php'),
		], 'config');
	}

	private function extendAuthGuard()
	{
		auth()->extend('paseto', function ($app, $name, array $config) {
			return new PasetoGuard(auth()->createUserProvider($config['provider']));
		});
	}
}
