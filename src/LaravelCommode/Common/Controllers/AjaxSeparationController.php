<?php
    namespace LaravelCommode\Common\Controllers;

    use Illuminate\Routing\Controller;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 11/15/14
     * Time: 3:27 AM
     */
    class AjaxSeparationController extends ResolvableController
    {
        protected $separateRequests = false;
        protected $allowAjax = true;

        public function callAction($method, $params = array())
        {
            $isAjax = app('request')->ajax();

            if (!$this->allowAjax && $isAjax)
            {
                return $this->missingMethod();
            }

            if ($this->separateRequests)
            {
                if ($isAjax) {
                    $method = 'ajax'.$method;
                }
            }

            return parent::callAction($method, $params);
        }
    } 