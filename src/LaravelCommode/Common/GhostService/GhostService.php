<?php namespace LaravelCommode\Common\GhostService;

    use LaravelCommode\Common\Constants\ServiceShortCuts;
    use LaravelCommode\Common\GhostService\GhostServices;
    use LaravelCommode\Common\Resolver\Resolver;
    use LaravelCommode\Common\CommodeCommonServiceProvider;

    use Illuminate\Foundation\AliasLoader;
    use Illuminate\Support\ServiceProvider;

    /**
     * Class GhostService
     *
     * Is a smart Illuminate\Support\ServiceProvider wrapper
     * which main purpose to give a developer an ability for lazyloading of
     * all dependent service providers before the instance is registered/booted.
     * And there're some more features that you might find useful.
     *
     * @author Volynov Andrew
     * @package LaravelCommode\Common\GhostService
     */
    abstract class GhostService extends ServiceProvider
    {
        /**
         * List of bound aliases
         * @var array
         */
        protected $aliases = [];
        /**
         * Will be triggered when the app's 'booting' event is triggered
         */
        abstract protected function launching();


        /**
         * Triggered when service is being registered
         */
        abstract protected function registering();

        /**
         * Returns application's Resolver instance
         * @return \LaravelCommode\Common\Resolver\Resolver
         */
        private function getResolver()
        {
            return $this->app->make(ServiceShortCuts::RESOLVER_SERVICE);
        }

        /**
         * Prepares service for launching. If LaravelCommode\Common\CommodeCommonServiceProvider
         * is not registered in your app config, it will be forced to launch. Loads all services
         * used by current GhostService instance.
         *
         * @return $this
         */
        private function prepareService()
        {
            /**
             * Check if current provider instance is not CommodeCommonServiceProvider -
             * if it is CommodeCommonServiceProvider, abort loading dependent services
             */
            if (CommodeCommonServiceProvider::class == static::class)
            {
                return $this;
            }

            /**
             * If CommodeCommonServiceProvider is not loaded yet, load it
             * and mark as registered in GhostServices
             */
            if (!($bound = $this->app->bound(ServiceShortCuts::CORE_INITIALIZED)))
            {
                $this->services([CommodeCommonServiceProvider::class]);
            }

            $this->with(ServiceShortCuts::GHOST_SERVICE, function(GhostServices $appServices) use ($bound) {
                /**
                 * If CommodeCommonServiceProvider is not loaded yet, load it
                 * and mark as registered in GhostServices
                 */
                if (!$bound)
                {
                    $appServices->register(CommodeCommonServiceProvider::class);
                }

                /**
                 * Get and register service providers in ServiceManager
                 */
                $services = $appServices->differUnique($this->uses(), true);

                /**
                 * Register service proviers in laravel app
                 */
                $this->services($services);

                /**
                 * Mark current service as registered
                 */
                $appServices->register(static::class);
            });

            return $this;
        }

        /**
         * Method is triggered when application 'booting' event is fired.
         * First it resolves all array of method names that is
         * extraxted from resolving() method. Then it calls
         * launching() method.
         */
        private function launchClosure()
        {
            foreach($this->resolving() as $methodName) {
                $this->getResolver()->method($this, $methodName, [], true);
            }


            $this->launching();
        }

        /**
         * Method prepares service provider for registering/launching.
         * It loads all dependent providers and binds 'booting' callbacks.
         */
        public function register()
        {

            if (CommodeCommonServiceProvider::class != static::class)
            {
                /**
                 * Prepare service
                 */
                $this->prepareService();

                /**
                 * Bind app's 'booting' event resolving, aliases and launching methods
                 */
                $this->app->booting($this->getResolver()->makeClosure(function () {

                    if (count($this->aliases) > 0)
                    {
                        $loader = AliasLoader::getInstance();
                        foreach($this->aliases as $facadeName => $facadeClass)
                        {
                            $loader->alias($facadeName, $facadeClass);
                        }
                    }

                    $this->launchClosure();
                }));

            }

            /**
             * Register service
             */
            $this->registering();
        }

        /**
         * An array of depended service providers that
         * current service provider uses.
         *
         * @return string[]
         */
        protected function uses()
        {
            return [];
        }

        /**
         * An array of method names that need to be resolved before
         * app's 'booting' event is triggered
         * @return string[]
         */
        protected function resolving()
        {
            return [];
        }

        /**
         * A useful helper method that allows you to avoid facades'
         * usage panic and work straight with IoC bindings. E.g.:
         *
         *      $this->with('view', function (\Illuminate\View\Factory $factory)
         *      {
         *          $factory->addExtension('my-ext', '/path/to/my/ext');
         *      });
         *
         *  Or:
         *
         *      $with = ['view', \Utils\Interfaces\IMenuService::class];
         *      $this->with($with, function (\Illuminate\View\Factory $factory, \Utils\Interfaces\IMenuService $service)
         *      {
         *          $this->doSomethingWithView($factory);
         *          $this->doSomethingWithService($service);
         *      });
         *
         * @param string|array $withService IoC bindings name or array of names
         * @param callable $do
         */
        protected function with($withService, callable $do)
        {
            if (is_string($withService))
            {
                $withService = [$withService];
            }

            foreach ($withService as $key => $item)
            {
                $withService[$key] = $this->app->make($item);
            }

            call_user_func_array($do, $withService);
        }

        /**
         * Registers array of service providers in
         * laravel application.
         * @param array $services
         */
        private function services(array $services = [])
        {
            foreach($services as $service)
            {
                $this->app->forceRegister($service, []);
            }
        }
    }