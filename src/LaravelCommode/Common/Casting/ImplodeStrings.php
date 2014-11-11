<?php
    namespace LaravelCommode\Common\Casting;

    use LaravelCommode\Common\Registry\AbstractRegistry;

    class ImplodeStrings extends AbstractRegistry
    {
        /**
         * @var string
         */
        private $separator;

        /**
         * @var array
         */
        protected $strings = [];

        public function __construct($separator = '')
        {
            $this->separator = $separator;
        }

        public function setSeparator($separator)
        {
            $this->separator = $separator;
            return $this;
        }

        public function getSeparator()
        {
            return $this->separator;
        }

        public function __toString()
        {
            return implode($this->getSeparator(), $this->strings);
        }

        protected function getContainerName()
        {
            return "strings";
        }
    }