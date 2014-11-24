<?php
    namespace GhostService;
    use LaravelCommode\Common\CommodeCommonServiceProvider;
    use LaravelCommode\Common\Constants\ServiceShortCuts;
    use LaravelCommode\Common\GhostService\GhostService;
    use LaravelCommode\Common\GhostService\GhostServices;
    use LaravelCommode\Common\Resolver\Resolver;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 11/24/14
     * Time: 1:57 AM
     */
    class GhostServiceTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @param $appMock
         * @return \PHPUnit_Framework_MockObject_MockObject
         */
        protected  function buildGhostServiceMock($appMock)
        {
            $mock = $this->getMockBuilder(GhostService::class)->setConstructorArgs(func_get_args())
                         ->setMethods(['uses'])
                         ->getMockForAbstractClass();

            $mock->expects($this->any())->method('launching')->will(
                $this->returnValue(null)
            );

            $mock->expects($this->any())->method('registering')->will(
                $this->returnValue(null)
            );

            return $mock;
        }

        /**
         * @return \Mockery\MockInterface
         */
        protected  function buildAppMock()
        {
            return \Mockery::mock('Illuminate\Foundation\Application');
        }

        public function testRegistrationEmpty()
        {
            $ghostServicesManager = new GhostServices();

            $resolverAppMock = $this->buildAppMock();

            $resolver = new Resolver($resolverAppMock);

            $appMock = $this->buildAppMock();

            $appMock->shouldReceive('bound')->once()->andReturn(false);
            $appMock->shouldReceive('forceRegister')->once();

            $appMock->shouldReceive('make')->twice()->andReturnUsing(function ($resolves) use ($ghostServicesManager, $resolver)
            {
                switch($resolves)
                {
                    case ServiceShortCuts::GHOST_SERVICE:
                        $this->assertSame($resolves, ServiceShortCuts::GHOST_SERVICE);
                        return $ghostServicesManager;
                    case ServiceShortCuts::RESOLVER_SERVICE:
                        $this->assertSame($resolves, ServiceShortCuts::RESOLVER_SERVICE);
                        return $resolver;
                }
            });

            $appMock->shouldReceive('booting')->once();


            /**
             * @var \PHPUnit_Framework_MockObject_MockObject|GhostService $service
             */
            $service = $this->buildGhostServiceMock($appMock);

            $service->expects($this->any())->method('uses')->will(
                $this->returnValue([])
            );

            $service->register();
        }

        public function testRegistration()
        {
            $ghostServicesManager = new GhostServices();

            $resolverAppMock = $this->buildAppMock();

            $resolver = new Resolver($resolverAppMock);

            $appMock = $this->buildAppMock();

            $appMock->shouldReceive('bound')->zeroOrMoreTimes()->andReturn(true, true);
            $appMock->shouldReceive('forceRegister')->never();

            $appMock->shouldReceive('make')->twice()->andReturnUsing(function ($resolves) use ($ghostServicesManager, $resolver)
            {
                switch($resolves)
                {
                    case ServiceShortCuts::GHOST_SERVICE:
                        $this->assertSame($resolves, ServiceShortCuts::GHOST_SERVICE);
                        return $ghostServicesManager;
                    case ServiceShortCuts::RESOLVER_SERVICE:
                        $this->assertSame($resolves, ServiceShortCuts::RESOLVER_SERVICE);
                        return $resolver;
                }
            });

            $appMock->shouldReceive('booting')->once();


            /**
             * @var \PHPUnit_Framework_MockObject_MockObject|GhostService $service
             */
            $service = $this->buildGhostServiceMock($appMock);

            $service->expects($this->any())->method('uses')->will(
                $this->returnValue([])
            );

            $service->register();
        }

        public function testUsesAllNew()
        {
            $register = ['Service1', 'Service2', 'Service3'];
            $expectedResult = array_merge([CommodeCommonServiceProvider::class], $register);

            $ghostServicesManager = new GhostServices();

            $resolverAppMock = $this->buildAppMock();

            $resolver = new Resolver($resolverAppMock);

            $appMock = $this->buildAppMock();

            $appMock->shouldReceive('bound')->zeroOrMoreTimes()->andReturn(false, true);

            $appMock->shouldReceive('forceRegister')->times(4)->andReturnUsing(function($service) use ($expectedResult)
            {
                $this->assertTrue(in_array($service, $expectedResult));
            });

            $appMock->shouldReceive('make')->twice()->andReturnUsing(function ($resolves) use ($ghostServicesManager, $resolver)
            {
                switch($resolves)
                {
                    case ServiceShortCuts::GHOST_SERVICE:
                        $this->assertSame($resolves, ServiceShortCuts::GHOST_SERVICE);
                        return $ghostServicesManager;
                    case ServiceShortCuts::RESOLVER_SERVICE:
                        $this->assertSame($resolves, ServiceShortCuts::RESOLVER_SERVICE);
                        return $resolver;
                }
            });

            $appMock->shouldReceive('booting')->once();


            /**
             * @var \PHPUnit_Framework_MockObject_MockObject|GhostService $service
             */
            $service = $this->buildGhostServiceMock($appMock);

            $service->expects($this->any())->method('uses')->will(
                $this->returnValue($register)
            );

            $service->register();

            $this->assertNotSameSize($ghostServicesManager->getRegistered(), $expectedResult);
            $this->assertSame(last($ghostServicesManager->getRegistered()), get_class($service));
        }

        public function testUses()
        {
            $register = ['Service1', 'Service2', 'Service3'];
            $expectedResult = array_merge([CommodeCommonServiceProvider::class], $register);

            $ghostServicesManager = new GhostServices();

            $resolverAppMock = $this->buildAppMock();

            $resolver = new Resolver($resolverAppMock);

            $appMock = $this->buildAppMock();

            $appMock->shouldReceive('bound')->zeroOrMoreTimes()->andReturn(false, true);

            $appMock->shouldReceive('forceRegister')->times(4)->andReturnUsing(function($service) use ($expectedResult)
            {
                $this->assertTrue(in_array($service, $expectedResult));
            });

            $appMock->shouldReceive('make')->twice()->andReturnUsing(function ($resolves) use ($ghostServicesManager, $resolver)
            {
                switch($resolves)
                {
                    case ServiceShortCuts::GHOST_SERVICE:
                        $this->assertSame($resolves, ServiceShortCuts::GHOST_SERVICE);
                        return $ghostServicesManager;
                    case ServiceShortCuts::RESOLVER_SERVICE:
                        $this->assertSame($resolves, ServiceShortCuts::RESOLVER_SERVICE);
                        return $resolver;
                }
            });

            $appMock->shouldReceive('booting')->once();


            /**
             * @var \PHPUnit_Framework_MockObject_MockObject|GhostService $service
             */
            $service = $this->buildGhostServiceMock($appMock);

            $service->expects($this->any())->method('uses')->will(
                $this->returnValue($register)
            );

            $service->register();

            $this->assertNotSameSize($ghostServicesManager->getRegistered(), $expectedResult);
            $this->assertSame(last($ghostServicesManager->getRegistered()), get_class($service));
        }

        public function tearDown()
        {
            \Mockery::close();
        }

    } 