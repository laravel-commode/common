<?php
/**
 * Created by PhpStorm.
 * User: madman
 * Date: 20.05.15
 * Time: 23:20
 */

namespace LaravelCommode\Common\GhostService;

class GhostServicesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GhostServices
     */
    private $ghostServices;

    protected function setUp()
    {
        $this->ghostServices = new GhostServices();
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testRegister()
    {
        /**
         * Testing fake service registration
         */
        $names = ['Service1', 'Service2', 'Service3'];
        $this->ghostServices->registers($names);

        $this->assertSame($names, $this->ghostServices->getRegistered());

        /**
         * Testing unique service registration
         */
        $names_2 = ['Service3', 'Service4'];
        $this->ghostServices->registers($names_2);

        $names[] = $names_2[1];

        $this->assertSame($names, $this->ghostServices->getRegistered());
    }
}
