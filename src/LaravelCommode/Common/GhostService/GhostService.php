<?php namespace LaravelCommode\Common\GhostService;

    use LaravelCommode\Common\Constants\ServiceShortCuts;
    use LaravelCommode\Common\Resolver\Resolver;
    use LaravelCommode\Common\GhostService\GhostServices;
    use Illuminate\Support\ServiceProvider;

    abstract class GhostService extends ServiceProvider
    {
        abstract public function launching();
        abstract public function registering();

        /**
         * @return \LaravelCommode\Common\Resolver\Resolver
         */
        protected function resolver()
        {
            return $this->app[ServiceShortCuts::RESOLVER_SERVICE];
        }

        private function prepareService()
        {
            $this->with([ServiceShortCuts::GHOST_SERVICE], function(GhostServices $appServices) {
                $services = array_diff($this->uses(), $appServices->getRegistered());
                $appServices->registers($services);
                $this->services($services, true);
            });
            return $this;
        }

        public function launchClosure()
        {
            foreach($this->resolving() as $methodName) {
                $this->resolver()->method($this, $methodName, []);
            }

            $this->launching();
        }

        public function register()
        {
            $this->prepareService();

            $this->registering();
            $this->app->booting($this->resolver()->methodToClosure($this, 'launchClosure'));
        }

        public function uses()
        {
            return [];
        }

        public function resolving()
        {
            return [];
        }

        /**
         * @param string|array $service
         * @param callable $do
         */
        public function with($service, callable $do)
        {
            if (is_string($service)) {
                $service = [$service];
            }

            foreach ($service as $key => $item) {
                $service[$key] = $this->app->make($item);
            }

            call_user_func_array($do, $service);
        }

        public function services(array $services = [])
        {
            foreach($services as $key => $service) {
                if (is_array($service)) {
                    $this->app->registerDeferredProvider(array_keys($service)[0]);
                } else {
                    $this->app->forceRegister($service, []);
                }
            }
        }

        public function deferedServices(array $services = [])
        {
            foreach($services as $service) {
                $this->app->registerDeferredProvider($service);
            }
        }
    }