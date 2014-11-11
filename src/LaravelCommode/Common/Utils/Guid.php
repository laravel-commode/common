<?php namespace LaravelCommode\Common\Utils;
        
    class Guid 
    {
        protected $Guid;
        /**
         * @var bool
         */
        private $wrap;
        /**
         * @var bool
         */
        private $lowerCase;

        public function __construct($lowerCase = true, $wrap = false)
        {
            $this->Guid = self::make($lowerCase, $wrap);
            $this->wrap = $wrap;
            $this->lowerCase = $lowerCase;
        }

        public function regenerate()
        {
            $this->Guid = self::make($this->lowerCase, $this->wrap);
            return $this;
        }

        public function __toString()
        {
            return $this->Guid;
        }

        public function get()
        {
            return $this->Guid;
        }

        static public function make($lowerCase = true, $wrap = false)
        {
            if (function_exists('com_create_guid')){
                return str_replace(["{", "}"], "", com_create_guid());
            } else {
                $charid = strtoupper(md5(uniqid(rand(), true)));
                $hyphen = chr(45);
                $uuid = ($wrap ? chr(123) : "")
                    .substr($charid, 0, 8).$hyphen.substr($charid, 8, 4).$hyphen
                    .substr($charid,12, 4).$hyphen.substr($charid,16, 4).$hyphen
                    .substr($charid,20,12).($wrap ? chr(125) : "");
                return $lowerCase ? strtolower($uuid) : $uuid;
            }
        }
    }