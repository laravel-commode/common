<?php
    namespace LaravelCommode\Common;

    use Illuminate\Foundation\AliasLoader;
    use LaravelCommode\Common\Constants\ServiceShortCuts;
    use LaravelCommode\Common\GhostService\GhostService;
    use LaravelCommode\Common\GhostService\GhostServices;
    use LaravelCommode\Common\Resolver\Resolver;
    use Illuminate\Support\ServiceProvider;

    /**
     * Class CommodeCommonServiceProvider
     *
     * Is a common service for all laravel-commode packages.
     * It binds Resolver and GhostService manager.
     *
     * @author Volynov Andrew
     * @package LaravelCommode\Common
     */
    class CommodeCommonServiceProvider extends GhostService
    {
        /**
         * Get the services provided by the provider.
         *
         * @return array
         */
        public function provides()
        {
            return [
                ServiceShortCuts::GHOST_SERVICE, ServiceShortCuts::RESOLVER_SERVICE
            ];
        }

        /**
         * Bootstrap the application events.
         *
         * @return void
         */
        public function boot()
        {
            $this->package('laravel-commode/common');
        }

        /**
         * Will be triggered when the app's 'booting' event is triggered
         */
        public function launching()
        {
            $this->app->alias('CommodeResolver', 'LaravelCommode\Common\Facades\Resolver');
        }

        /**
         * Triggered when service is being registered
         */
        public function registering()
        {
            $this->app->bindShared(ServiceShortCuts::RESOLVER_SERVICE, function()
            {
                return new Resolver($this->app);
            });

            $this->app->bindShared(ServiceShortCuts::GHOST_SERVICE, function()
            {
                return new GhostServices($this->app);
            });

            $this->app->bind(ServiceShortCuts::CORE_INITIALIZED, true);
        }
    }
