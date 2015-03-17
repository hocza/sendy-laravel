<?php namespace Hocza\Sendy;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class SendyServiceProvider extends ServiceProvider {

	/**
	* Indicates if loading of the provider is deferred.
	*
	* @var bool
	*/
	protected $defer = false;

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('hocza/sendy');
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['sendy'] = $this->app->share(function($app)
		{
			return new Sendy($app['config']);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('sendy');
	}

}
