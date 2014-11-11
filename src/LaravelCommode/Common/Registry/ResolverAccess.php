<?php namespace Dubpub\LaravelCommode\Common\Registry;

    abstract class ResolverAccess extends AbstractRegistryObject
    {
        protected function onSet(&$offset, $value)
        {
            if (is_string($value)) {
                $value = app()->make($value);
            }

            return $value;
        }
    }