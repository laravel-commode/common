<?php
    namespace LaravelCommode\Common\Facades;
    use Illuminate\Support\Facades\Facade;
    use LaravelCommode\Common\Constants\ServiceShortCuts;

    /**
     * Class Resolver
     *
     * Facade for laravel-commode/common resolver
     *
     * @author Volynov Andrey
     * @package LaravelCommode\Common\Facades
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