<?php

    use Illuminate\Foundation\AliasLoader;
    use LaravelCommode\Common\CommodeCommonServiceProvider;
    use LaravelCommode\Common\Constants\ServiceShortCuts;
    use LaravelCommode\Common\GhostService\GhostService;
    use LaravelCommode\Common\GhostService\GhostServices;
    use LaravelCommode\Common\Resolver\Resolver;
    use Illuminate\Support\ServiceProvider;

    /**
     * Class CommodeCommonServiceProvider
     *
     * Is a common service for all laravel-commode packages.
     * It binds Resolver and GhostService manager.
     *
     * @author Volynov Andrew
     * @package LaravelCommode\Common
     */
    class CommodeCommonServiceProviderTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * @return \Mockery\MockInterface
         */
        protected  function buildAppMock()
        {
            return \Mockery::mock('Illuminate\Foundation\Application');
        }

        /**
         * @param $appMock
         * @return CommodeCommonServiceProvider
         */
        protected  function buildService($appMock)
        {
            return new CommodeCommonServiceProvider($appMock);
        }

        public function testRegistering()
        {
            $appMock = $this->buildAppMock();

            $appMock->shouldReceive('bindShared')->times(1)->with(
                ServiceShortCuts::RESOLVER_SERVICE, \Mockery::on(function ($closure) {
                    $this->assertTrue($callable = is_callable($closure));
                    $this->assertTrue($resolver = ($closure() instanceof Resolver));
                    return $closure && $resolver;
                })
            );

            $appMock->shouldReceive('bindShared')->times(1)->with(
                ServiceShortCuts::GHOST_SERVICE, \Mockery::on(function ($closure) {
                    $this->assertTrue($callable = is_callable($closure));
                    $this->assertTrue($ghostServices = ($closure() instanceof GhostServices));
                    return $closure && $ghostServices;
                })
            );

            $appMock->shouldReceive('bind')->once();

            $appMock->shouldReceive('booting')->once()->with(\Mockery::on(function ($booting)
            {
                $this->assertTrue(is_callable($booting));
                return is_callable($booting);
            }));

            $service = $this->buildService($appMock);

            $service->register();
        }

        public function testLaunching()
        {
            $appMock = $this->buildAppMock();

            $service = $this->buildService($appMock);

            $this->assertNull($service->launching());
        }

        public function testBoot()
        {
            $appMock = $this->buildAppMock();

            $serviceMock = $this->getMockBuilder('LaravelCommode\Common\CommodeCommonServiceProvider')
                                ->setConstructorArgs([$appMock])
                                ->setMethods(['package'])
                                ->getMock();

            $serviceMock->expects($this->once())->method('package');

            $serviceMock->boot();
        }

        public function testProvides()
        {
            $appMock = $this->buildAppMock();

            $service = $this->buildService($appMock);

            $reflection = new \ReflectionClass('LaravelCommode\Common\Constants\ServiceShortCuts');

            $provided = $service->provides();

            $this->assertTrue(in_array(ServiceShortCuts::GHOST_SERVICE, $provided));
            $this->assertTrue(in_array(ServiceShortCuts::RESOLVER_SERVICE, $provided));
        }
    }