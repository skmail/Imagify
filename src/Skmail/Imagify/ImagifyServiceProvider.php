<?php namespace Skmail\Imagify;

use Illuminate\Support\ServiceProvider;

class ImagifyServiceProvider extends ServiceProvider {

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
		$this->package('skmail/imagify');

        $this->registerRoute();
    }


    public function registerRoute(){
        $route = $this->app['config']->get('imagify::route');
        $this->app['router']
            ->get($route . '/{method}/{width}/{height}/{source}','Skmail\Imagify\Controllers\ImagifyController@response')
            ->where('width', '[0-9]+')
            ->where('height', '[0-9]+')
            ->where('source', '((([^0-9A-Fa-f]{3}|[^0-9A-Fa-f]{6})?).*?.)');
    }
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
