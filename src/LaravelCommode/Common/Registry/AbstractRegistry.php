<?php namespace LaravelCommode\Common\Registry;
        
    use ArrayAccess;
    use Countable;
    use Illuminate\Contracts\Support\Arrayable;
    use Iterator;

    use Illuminate\Support\Contracts\ArrayableInterface;

    /**
     * Class AbstractRegistry
     *
     * Is an abstract class, that implements some of stdlib interfaces
     * (ArrayAccess, Iterator, Countable) out of box. Provides onGet and onSet event
     * by overriding AbstractRegistry. To use it simply inherit your class of AbstractRegistry,
     * add public or protected value, that would be used as your container and return it's name from
     * implemented getContainerName(). Class is supposed to be treated as array access only. For ObjectAccess
     * read about AbstractRegistryObject
     *
     * @author Volynov Andrey
     * @package LaravelCommode\Common\Registry
     */
    abstract class AbstractRegistry implements ArrayAccess, Iterator, Countable, Arrayable
    {
        /**
         * Returns name of protected/public property that should be used as container
         * @return string Container property name
         */
        abstract protected function getContainerName();

        /**
         * Works as key => value setter.
         *
         * @param $key string Container $key that is being set. Note that $key is passed by reference.
         * @param $value mixed Container $value that is being set.
         * @return mixed Returns value that should be stored.
         */
        protected function onSet(&$key, $value)
        {
            return $value;
        }

        /**
         * Works as key getter.
         *
         * @param $key string Container $key that is being set. Note that $key is passed by reference.
         * @param $value mixed Container $value that is being set.
         * @return mixed Returns value that should be returned.
         */
        protected function onGet($key, $value)
        {
            return $value;
        }

        /**
         * Merges passed $array with instance container
         *
         * @param array|mixed[] $array values to merge with instance container
         * @return $this
         */
        public function merge(array $array)
        {
            $this->{$this->getContainerName()} = array_merge(
                $this->{$this->getContainerName()}, $array
            );

            return $this;
        }

        /**
         * Merges passed AbstractRegistry $registry contained
         * with instance container
         *
         * @param AbstractRegistry $registry
         * @return $this
         */
        public function mergeRegistry(AbstractRegistry $registry)
        {
            return $this->merge($registry->toArray());
        }

        /**
         * Implementation of ArrayAccess offsetExists($offset)
         * @param string|int $offset
         * @return bool
         */
        public function offsetExists($offset)
        {
            return isset($this->{$this->getContainerName()}[$offset]);
        }

        /**
         * Implementation of ArrayAccess offsetGet($offset)
         * @param string|int $offset
         * @return mixed
         */
        public function offsetGet($offset)
        {
            return $this->onGet($offset, $this->{$this->getContainerName()}[$offset]);
        }

        /**
         * Implementation of ArrayAccess offsetSet($offset, $value)
         *
         * @param string|int $offset
         * @param mixed $value
         */
        public function offsetSet($offset, $value)
        {
            $offset = $offset == null ? count($this) : $offset;
            $this->{$this->getContainerName()}[$offset] = $this->onSet($offset, $value);
        }

        /**
         * Implementation of ArrayAccess offsetUnset($offset)
         * @param string|int $offset
         */
        public function offsetUnset($offset)
        {
            unset($this->{$this->getContainerName()}[$offset]);
        }

        /**
         * Implementation of Iterator current()
         * @return mixed
         */
        public function current()
        {
            return current($this->{$this->getContainerName()});
        }

        /**
         * Implementation of Iterator current()
         * @return mixed|void
         */
        public function next()
        {
            return next($this->{$this->getContainerName()});
        }

        /**
         * Implementation of Iterator key()
         * @return mixed
         */
        public function key()
        {
            return key($this->{$this->getContainerName()});
        }

        /**
         * Implementation of Iterator valid()
         * @return bool
         */
        public function valid()
        {
            return $this->key() !== null;
        }

        /**
         * Implementation of Iterator rewind()
         * @return mixed|void
         */
        public function rewind()
        {
            return reset($this->{$this->getContainerName()});
        }

        /**
         * Implementation of Countable count()
         * @return int
         */
        public function count()
        {
            return count($this->{$this->getContainerName()});
        }

        /**
         * Converts AbstractRegistry to array
         *
         * @return mixed[]|array Container values
         */
        public function toArray()
        {
            return $this->{$this->getContainerName()};
        }
    }