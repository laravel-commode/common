<?php namespace LaravelCommode\Common\Registry;

    /**
     * Class AbstractRegistryObject
     *
     * Is an abstract class extends AbstractRegistry
     * and allows accessing it like object
     *
     * @author Volynov Andrey
     * @package LaravelCommode\Common\Registry
     */
    abstract class AbstractRegistryObject extends AbstractRegistry
    {
        /**
         * Triggers AbstractRegistry::offsetGet($offset)
         *
         * @param int|string $offset Offset index
         * @return null|mixed  Offset value
         */
        public function __get($offset)
        {
            return isset($this[$offset]) ? $this->offsetGet($offset) : null;
        }

        /**
         * Triggers AbstractRegistry::offsetSet($offset, $value)
         * @param int|string $offset Offset index
         * @param mixed $value  Offset value
         */
        public function __set($offset, $value)
        {
            $this[$offset] = $value;
        }
    }