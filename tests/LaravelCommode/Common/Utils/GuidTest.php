<?php
/**
 * Created by PhpStorm.
 * User: madman
 * Date: 20.05.15
 * Time: 23:31
 */

namespace LaravelCommode\Common\Utils;

class GuidTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Guid
     */
    private $guid;

    protected function setUp()
    {
        $this->guid = new Guid();
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testGetAndToString()
    {
        $this->assertSame($this->guid->__toString(), $this->guid->get());
    }

    public function testRegenerate()
    {
        $this->assertNotSame($this->guid->get(), $this->guid->regenerate());
    }

    public function testStaticCall()
    {
        $this->assertNotSame($this->guid->get(), Guid::make());
    }
}
