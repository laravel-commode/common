<?php
    namespace LaravelCommode\Common\Controllers;

    use Illuminate\Routing\Controller;
    use LaravelCommode\Common\Resolver\Resolver;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 11/15/14
     * Time: 3:46 AM
     */
    class CommodeController extends Controller
    {
        protected $resolveMethods = true;

        protected $separateRequests = false;
        protected $allowAjax = true;

        private function checkParametersResolving($method, $params = array())
        {
            if (!$this->resolveMethods) {
                return $params;
            }

            $resolver = new Resolver(app());
            return $resolver->resolveMethodParameters($this, $method, $params);
        }

        private function checkAllowAjax($isAjax = null)
        {
            $isAjax = is_null($isAjax) ? app('request')->ajax() : $isAjax;
            return $isAjax && $this->allowAjax;
        }

        private function checkAjaxMethod($method, $isAjax = null)
        {
            $isAjax = is_null($isAjax) ? app('request')->ajax() : $isAjax;

            if ($this->separateRequests && $isAjax)
            {
                return 'ajax_'.$method;
            }

            return $method;
        }

        public function callAction($method, $params = array())
        {
            $isAjax = app('request')->ajax();

            if (!$this->checkAllowAjax($isAjax))
            {
                return $this->missingMethod();
            }

            $method = $this->checkAjaxMethod($method, $isAjax);
            $params = $this->checkParametersResolving($method, $params);

            return parent::callAction($method, $params);
        }
    }