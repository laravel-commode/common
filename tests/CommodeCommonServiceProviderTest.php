<?php
    namespace LaravelCommode\Common;

    use Illuminate\Foundation\AliasLoader;
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
            $appMock->shouldReceive('bindShared')->twice();
            $appMock->shouldReceive('bind')->once();

            $service = $this->buildService($appMock);

            $service->register();
        }

        public function testLaunching()
        {
            $appMock = $this->buildAppMock();

            $service = $this->buildService($appMock);

            $this->assertNull($service->launching());
        }

        public function testProvides()
        {
            $appMock = $this->buildAppMock();

            $service = $this->buildService($appMock);

            $reflection = new \ReflectionClass(ServiceShortCuts::class);

            $provided = $service->provides();

            $this->assertTrue(in_array(ServiceShortCuts::GHOST_SERVICE, $provided));
            $this->assertTrue(in_array(ServiceShortCuts::RESOLVER_SERVICE, $provided));
        }
    }