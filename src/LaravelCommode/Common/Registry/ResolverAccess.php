<?php namespace LaravelCommode\Common\Registry;

    /**
     * Class ResolverAccess
     *
     * Is an abstract AbstractRegistryObject, where each set values
     * are passed through laravel's app IoC. For e.g
     *
     *      $resolverAccess->viewFactory = 'view';
     *      var_dump($resolverAccess->viewFactory instanceof \Illuminate\View\Factory); // true
     *
     * @author Volynov Andrey
     * @package LaravelCommode\Common\Registry
     */
    abstract class ResolverAccess extends AbstractRegistryObject
    {
        /**
         * Resolves value that's being set
         *
         * @param string $offset offset key
         * @param mixed $value offset value
         * @return mixed
         */
        protected function onSet(&$offset, $value)
        {
            if (is_string($value)) {
                $value = app()->make($value);
            }

            return $value;
        }
    }