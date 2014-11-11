<?php namespace Dubpub\LaravelCommode\Common\Resolver;

    use Closure;
    use Illuminate\Foundation\Application;
    use ReflectionMethod;
    use ReflectionFunction;
    use ReflectionParameter;

    class Resolver
    {
        /**
         * @var \Illuminate\Foundation\Application
         */
        private $laraApp;

        public function __construct(Application $laraApp = null)
        {
            if (!is_null($laraApp)) {
                $this->laraApp = $laraApp;
            } else {
                $this->laraApp = app();
            }
        }

        /**
         * @param ReflectionParameter $parameter
         * @param $reflectionKey
         * @param array $params
         * @return bool|string
         */
        protected function isResolvable(ReflectionParameter $parameter, $reflectionKey, $params = array())
        {
            if (isset($params[$reflectionKey])) return false;

            if (preg_match('/\[\s\<\w+?>\s([\w\\\\]+)/s', $parameter->__toString(), $matches))
            {
                $canBeCreated = isset($matches[1]) && !$parameter->isArray() && is_string($matches[1]);
                $existsOrBound = class_exists($matches[1]) || $this->laraApp->bound($matches[1]);

                return ($canBeCreated && $existsOrBound) ? $matches[1] : false;
            }

            return false;
        }

        protected function resolveParams()
        {

        }

        /**
         * @param ReflectionParameter[] $reflectionParams
         * @param array $params
         * @return array
         */
        public function resolve($reflectionParams, $params = array())
        {
            foreach($reflectionParams as $key => $reflectionParam)
            {
                if ($registryName = $this->isResolvable($reflectionParam, $key, $params))
                {
                    $params[$key] = $this->laraApp->make($registryName);//\App ::make();
                }
            }

            return $params;
        }

        public function method($class, $method, $params = array())
        {
            $reflection = new ReflectionMethod($class, $method);
            $resolved = $this->resolve($reflection->getParameters(), $params);
            return call_user_func_array([$class, $method], $resolved);
        }

        public function methodToClosure($class, $method)
        {
            return function () use ($class, $method)
            {
                return $this->method($class, $method, func_get_args());
            };
        }

        public function closure(Closure $closure, $params = array())
        {
            $reflection = new \ReflectionFunction($closure);
            $resolved = $this->resolve($reflection->getParameters(), $params);
            return call_user_func_array($closure, $resolved);
        }

        public function makeClosure(Closure $closure)
        {
            return function () use ($closure)
            {
                return $this->closure($closure, func_get_args());
            };
        }
    }