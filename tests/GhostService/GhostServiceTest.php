<?php
    namespace GhostService;
    use LaravelCommode\Common\GhostService\GhostService;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 11/24/14
     * Time: 1:57 AM
     */
    class GhostServiceTest extends \PHPUnit_Framework_TestCase
    {
        protected  function buildGhostServiceMock($appMock)
        {

            return $this->getMockBuilder(GhostService::class)->setConstructorArgs(func_get_args());
        }

        protected  function buildAppMock()
        {
            return \Mockery::mock('Illuminate\Foundation\Application');
        }

        public function testRegistration()
        {

        }

        public function teadDown()
        {
            \Mockery::close();
        }

    } 