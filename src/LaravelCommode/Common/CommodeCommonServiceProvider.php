<?php
    namespace LaravelCommode\Common;

    use Illuminate\Foundation\AliasLoader;
    use LaravelCommode\Common\Constants\ServiceShortCuts;
    use LaravelCommode\Common\GhostService\GhostService;
    use LaravelCommode\Common\GhostService\GhostServices;
    use LaravelCommode\Common\Resolver\Resolver;
    use Illuminate\Support\ServiceProvider;

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

        public function boot()
        {
            $this->package('laravel-commode/common');
        }

        public function launching()
        {
            $loader = AliasLoader::getInstance();
            $loader->alias('CommodeResolver', 'LaravelCommode\Common\Facades\Resolver');
        }

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

            $this->app->bind('commode.loaded', true);
        }
    }
