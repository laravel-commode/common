<?php namespace Dubpub\LaravelCommode\Common\Registry;

    abstract class AbstractRegistryObject extends AbstractRegistry
    {
        public function __get($offset)
        {
            return isset($this[$offset]) ? $this[$offset] : null;
        }

        public function __set($offset, $value)
        {
            $this[$offset] = $value;
        }
    }