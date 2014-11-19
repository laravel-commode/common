<?php
    namespace LaravelCommode\Common\Controllers;

    use Illuminate\Routing\Controller;
    use LaravelCommode\Common\Constants\ServiceShortCuts;
    use LaravelCommode\Common\Resolver\Resolver;

    /**
     * Class CommodeController
     *
     * CommodeController is a laravel controller wrapper with some extended possibilities.
     * It can resolve methods if protected $resolveMethods = true (true is the default value)
     * and separate ajax calls like calling ajax_getIndex() if method
     * getIndex() exists, request is ajax and protected $separateRequests = true (false is the default value)
     *
     * @author Volynov Andrey
     * @package LaravelCommode\Common\Controllers
     */
    class CommodeController extends Controller
    {
        /**
         * Determines if called methods need to be resolved.
         * @var bool
         */
        protected $resolveMethods = true;

        /**
         * Determines if all ajax methods are prefixed with 'ajax_'
         * @var bool
         */
        protected $separateRequests = false;

        /**
         * Determines resolving is enabled. If it's enabled then
         * it returns method resolved parameters, otherwise it
         * returns parameters as they are.
         *
         * @param string $method Method name.
         * @param array $params Array of known parameters.
         * @return mixed
         */
        private function checkParametersResolving($method, $params = array())
        {
            if (!$this->resolveMethods)
            {
                return $params;
            }

            $resolver = app(ServiceShortCuts::RESOLVER_SERVICE);
            return $resolver->resolveMethodParameters($this, $method, $params);
        }

        /**
         * Determines ajax separation is enabled. If it's enabled then
         * it returns method's name prefixed with 'ajax', otherwise it
         * returns method's name as it is.
         *
         * @param string $method Method name.
         * @param bool|null $isAjax Determines if request is ajax.
         * @return string
         */
        private function checkAjaxMethod($method, $isAjax = null)
        {
            $isAjax = is_null($isAjax) ? app('request')->ajax() : $isAjax;

            if ($this->separateRequests && $isAjax)
            {
                return 'ajax_'.$method;
            }

            return $method;
        }

        /**
         * Calls controller action.
         *
         * @param string $method
         * @param array $params
         * @return mixed
         */
        public function callAction($method, $params = array())
        {
            $isAjax = app('request')->ajax();

            $method = $this->checkAjaxMethod($method, $isAjax);
            $params = $this->checkParametersResolving($method, $params);

            return parent::callAction($method, $params);
        }
    }