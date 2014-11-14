<?php
    namespace LaravelCommode\Common\Controllers;

    use Illuminate\Routing\Controller;
    use LaravelCommode\Common\Resolver\Resolver;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 11/15/14
     * Time: 3:27 AM
     */
    class ResolvableController extends Controller
    {
        protected $resolveMethods = true;

        public function callAction($method, $params = array())
        {
            if ($this->resolveMethods) {
                $resolver = new Resolver(app());
                $params = $resolver->resolveMethodParameters($this, $method, $params);
            }

            return parent::callAction($method, $params);
        }
    } 