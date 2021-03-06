<?php

namespace LaravelCommode\Common\Utils;

/**
 * Class Guid
 *
 * Class like CSharp's System.Guid. Basicly simple
 * Guid generator in case function com_create_guid()
 * does not exists.
 *
 * @author Volynov Andrey
 * @package LaravelCommode\Common\Utils
 */
class Guid
{
    /**
     * Keeps generated guid value
     * @var string
     */
    protected $guid;

    /**
     * Indicates if generated guid needs to be wrapped by curly brackets
     * @var bool
     */
    private $wrap;

    /**
     * Indicates if generated guid will be in lower case
     * @var bool
     */
    private $lowerCase;

    /**
     * Guid constructor
     *
     * @param bool $lowerCase Defines if generated guid will be in lower case.
     * @param bool $wrap Defined if generated guid needs to be wrapped by curly brackets.
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
     * @param bool $lowerCase Defines if generated guid will be in lower case.
     * @param bool $wrap Defined if generated guid needs to be wrapped by curly brackets.
     * @return string
     */
    public static function make($lowerCase = true, $wrap = false)
    {
        $charid = strtoupper(md5(uniqid(rand(), true)));

        $hyphen = chr(45);

        $uuid = ($wrap ? "{" : "")
            .substr($charid, 0, 8).$hyphen.substr($charid, 8, 4).$hyphen
            .substr($charid, 12, 4).$hyphen.substr($charid, 16, 4).$hyphen
            .substr($charid, 20, 12).($wrap ? "}" : "");

        return $lowerCase ? strtolower($uuid) : $uuid;
    }
}
