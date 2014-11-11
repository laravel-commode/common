<?php
	namespace Dubpub\LaravelCommode\Common;

	use Dubpub\LaravelCommode\Common\Constants\ServiceShortCuts;
	use Dubpub\LaravelCommode\Common\GhostService\GhostServices;
	use Dubpub\LaravelCommode\Common\Resolver\Resolver;
    use Illuminate\Support\ServiceProvider;

    class CommodeCommonServiceProvider extends ServiceProvider
	{

		/**
		 * Get the services provided by the provider.
		 *
		 * @return array
		 */
		public function provides()
		{
			return array(
				'dubpub.utils.resolver',
				'dubpub.utils.ghostservices'
			 );
		}

        public function boot()
        {
            $this->package('dubpub/laravel-utils');
        }

        /**
         * Register the service provider.
         *
         * @return void
         */
        public function register()
        {
            $this->app->bindIf(ServiceShortCuts::RESOLVER_SERVICE, function($app){
                return new Resolver($app);
            }, true);

            $this->app->bindIf(ServiceShortCuts::GHOST_SERVICE, function($app){
                return new GhostServices();
            }, true);
        }
    }
