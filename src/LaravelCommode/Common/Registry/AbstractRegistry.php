<?php namespace LaravelCommode\Common\Registry;
        
    use ArrayAccess;
    use Illuminate\Support\Contracts\ArrayableInterface;

    abstract class AbstractRegistry implements ArrayAccess, \Iterator, ArrayableInterface, \Countable
    {
        abstract protected function getContainerName();

        protected function onSet(&$key, $value)
        {
            return $value;
        }

        protected function onGet($key, $value)
        {
            return $value;
        }

        protected function container(&$container = [])
        {
            $name = $this->getContainerName();
            $container = &$this->{$name};
            return $container;
        }

        public function offsetExists($offset)
        {
            $this->container($container);
            return isset($this->{$this->getContainerName()}[$offset]);
        }

        public function offsetGet($offset)
        {
            return $this->onGet($offset, $this->{$this->getContainerName()}[$offset]);
        }

        public function offsetSet($offset, $value)
        {
            $offset = $offset == null ? count($this) : $offset;
            $this->{$this->getContainerName()}[$offset] = $this->onSet($offset, $value);
        }

        public function offsetUnset($offset)
        {
            unset($this->{$this->getContainerName()}[$offset]);
        }

        public function current()
        {
            return current($this->{$this->getContainerName()});
        }

        public function next()
        {
            return next($this->{$this->getContainerName()});
        }

        public function key()
        {
            return key($this->{$this->getContainerName()});
        }

        public function valid()
        {
            return $this->key() !== null;
        }

        public function rewind()
        {
            return reset($this->{$this->getContainerName()});
        }

        public function toArray()
        {
            return $this->{$this->getContainerName()};
        }

        public function merge(array $array)
        {
            $this->{$this->getContainerName()} = array_merge(
                $this->{$this->getContainerName()}, $array
            );
        }

        public function count()
        {
            return count($this->{$this->getContainerName()});
        }
    } 