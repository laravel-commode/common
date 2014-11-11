<?php
	namespace LaravelCommode\Common;

	use LaravelCommode\Common\Constants\ServiceShortCuts;
	use LaravelCommode\Common\GhostService\GhostServices;
	use LaravelCommode\Common\Resolver\Resolver;
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
				'commode.common.resolver',
				'commode.common.ghostservices'
			 );
		}

        public function boot()
        {
            $this->package('laravel-commode/common');
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
