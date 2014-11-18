<?php
    namespace LaravelCommode\Common\Facades;
    use Illuminate\Support\Facades\Facade;
    use LaravelCommode\Common\Constants\ServiceShortCuts;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 11/19/14
     * Time: 1:06 AM
     */
    class Resolver extends Facade
    {

        /**
         * Get the registered name of the component.
         *
         * @return string
         */
        protected static function getFacadeAccessor() { return ServiceShortCuts::RESOLVER_SERVICE; }
    }