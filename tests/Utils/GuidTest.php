<?php namespace Utils;
use LaravelCommode\Common\Utils\Guid;

/**
 * Created by PhpStorm.
 * User: madman
 * Date: 11/23/14
 * Time: 9:22 PM
 */
    class GuidTest extends \PHPUnit_Framework_TestCase
    {
        public function testGuidStatisMake()
        {
            $string = Guid::make(true);
            $this->assertTrue(mb_strtolower($string) == $string);

            $string = Guid::make(false);
            $this->assertTrue(mb_strtoupper($string) == $string);

            $string = Guid::make(true, true);
            $this->assertStringEndsWith('}', $string);
            $this->assertStringStartsWith('{', $string);

            $string = Guid::make();
            $this->assertStringEndsNotWith('}', $string);
            $this->assertStringStartsNotWith('{', $string);
        }

        public function testGuidGet()
        {
            $guid = new Guid();
            $this->assertSame($guid->get(), $guid->__toString());
        }

        public function testGuidToString()
        {
            $guid = new Guid();
            $this->assertSame($guid->get(), $guid->__toString());

            $this->assertSame($guid->get(), $guid."");
        }

        public function testRegenerate()
        {
            $guid = new Guid();
            $oldValue = $guid->get();
            $guid->regenerate();
            $this->assertNotSame($oldValue, $guid->get());
        }
    } 