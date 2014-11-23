<?php
    namespace GhostService;
    use LaravelCommode\Common\GhostService\GhostServices;

    /**
 * Created by PhpStorm.
 * User: madman
 * Date: 11/24/14
 * Time: 1:57 AM
 */
    class GhostServicesTest extends \PHPUnit_Framework_TestCase
    {
        protected function getInstance()
        {
            return new GhostServices();
        }

        public function testRegister()
        {
            $manager = $this->getInstance();

            $serviceList = ['FirstServiceProvider', 'SecondServiceProvider'];

            $manager->register($serviceList[0]);

            $this->assertNotSame($manager->getRegistered(), $serviceList);
            $this->assertTrue(in_array($serviceList[0], $manager->getRegistered()));

            $manager->register($serviceList[1]);

            $this->assertSame($manager->getRegistered(), $serviceList);
            $this->assertTrue(in_array($serviceList[1], $manager->getRegistered()));
        }

        public function testRegisters()
        {
            $manager = $this->getInstance();

            $serviceList = ['FirstServiceProvider', 'SecondServiceProvider'];

            $manager->registers($serviceList);

            $this->assertSame($manager->getRegistered(), $serviceList);
        }

        public function testIsRegistered()
        {
            $manager = $this->getInstance();

            $serviceList = ['FirstServiceProvider', 'SecondServiceProvider'];

            $manager->registers($serviceList);

            $this->assertTrue($manager->isRegistered($serviceList[0]));
            $this->assertFalse($manager->isRegistered('OtherServiceProvider'));
        }

        public function testGetRegistered()
        {
            $manager = $this->getInstance();

            $serviceList = ['FirstServiceProvider', 'SecondServiceProvider'];

            $manager->registers($serviceList);

            $this->assertSame($manager->getRegistered(), $serviceList);
        }

        public function testDifferUnique()
        {
            $manager = $this->getInstance();

            $serviceList = ['FirstServiceProvider', 'SecondServiceProvider'];
            $otherServiceList = ['OnceServiceProvider', 'UponServiceProvider'];

            $manager->registers($serviceList);

            $this->assertSame(
                array_values($manager->differUnique(array_merge($serviceList, $otherServiceList))),
                array_values($otherServiceList)
            );

            $manager->differUnique(array_merge($serviceList, $otherServiceList), true);

            $this->assertSame(
                $manager->getRegistered(), array_merge($serviceList, $otherServiceList)
            );

        }
    } 