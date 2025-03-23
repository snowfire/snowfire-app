<?php

namespace Snowfire\App;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Route;

class SnowfireServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->loadRoutesFrom(__DIR__ . '/../../routes.php');

		// Config publishing
		$this->publishes([
			__DIR__ . '/../../config/snowfire.php' => config_path('snowfire.php'),
		], 'snowfire-config');

		// Migrations publishing
		$this->publishes([
		    __DIR__.'/../../migrations/' => database_path('migrations')
		], 'snowfire-migrations');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__ . '/../../config/snowfire.php', 'snowfire'
		);

		$this->app->singleton('snowfire', function($app)
		{
			$defaultConfig = [
				'acceptUrl' => route('snowfire.accept'),
				'uninstallUrl' => route('snowfire.uninstall'),
				'tabUrl' => route('snowfire.tab'),
				'actions' => [],
			];

			$config = array_merge(
				$defaultConfig,
				Config::get('snowfire')
			);

			return new Snowfire($config);
		});

		AliasLoader::getInstance()->alias('Snowfire', 'Snowfire\App\Facades\Snowfire');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['snowfire'];
	}

}
