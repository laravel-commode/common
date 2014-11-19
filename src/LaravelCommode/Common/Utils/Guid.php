<?php namespace LaravelCommode\Common\Utils;

    /**
     * Class Guid
     *
     * Class like CSharp's System.Guid. Basicly simple
     * Guid generator in case function com_create_guid()
     * does not exists.
     *
     * @package LaravelCommode\Common\Utils
     */
    class Guid
    {
        protected $guid;
        /**
         * @var bool
         */
        private $wrap;
        /**
         * @var bool
         */
        private $lowerCase;

        /**
         * Guid constructor
         *
         * @param bool $lowerCase Defines if generated guid will be in lower case
         * @param bool $wrap Defined if generated guid needs to be wrapped by curly brackets
         */
        public function __construct($lowerCase = true, $wrap = false)
        {
            $this->guid = self::make($lowerCase, $wrap);
            $this->wrap = $wrap;
            $this->lowerCase = $lowerCase;
        }

        /**
         * Regenerated Guid value
         * @return $this
         */
        public function regenerate()
        {
            $this->guid = self::make($this->lowerCase, $this->wrap);
            return $this;
        }

        /**
         * Converts instance to string (guid value)
         * @return string
         */
        public function __toString()
        {
            return $this->guid;
        }

        /**
         * Returns guid value
         * @return string
         */
        public function get()
        {
            return $this->guid;
        }

        /**
         * Generates guid string
         *
         * @param bool $lowerCase Defines if generated guid will be in lower case
         * @param bool $wrap Defined if generated guid needs to be wrapped by curly brackets
         * @return string
         */
        static public function make($lowerCase = true, $wrap = false)
        {
            if (function_exists('com_create_guid')){
                $guid = com_create_guid();

                if ($lowerCase) {
                    $guid = mb_strtolower($guid);
                }

                if (!$wrap) {
                    return str_replace(["{", "}"], "", com_create_guid());
                }

                return $guid;
            } else {
                $charid = strtoupper(md5(uniqid(rand(), true)));
                $hyphen = chr(45);
                $uuid = ($wrap ? "{" : "")
                    .substr($charid, 0, 8).$hyphen.substr($charid, 8, 4).$hyphen
                    .substr($charid,12, 4).$hyphen.substr($charid,16, 4).$hyphen
                    .substr($charid,20,12).($wrap ? "}" : "");
                return $lowerCase ? strtolower($uuid) : $uuid;
            }
        }
    }