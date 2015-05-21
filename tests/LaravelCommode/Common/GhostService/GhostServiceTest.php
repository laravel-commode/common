<?php

namespace LaravelCommode\Common\GhostService;

use Illuminate\Foundation\Application;
use LaravelCommode\Common\CommodeCommonServiceProvider;
use LaravelCommode\Common\Resolver\Resolver;
use PHPUnit_Framework_MockObject_MockObject as Mock;

class GhostServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GhostService|Mock
     */
    private $ghostService;

    /**
     * @var Application|Mock
     */
    private $applicationMock;

    /**
     * @var CommodeCommonServiceProvider|Mock
     */
    private $commonGhostService;

    /**
     * @var GhostServices
     */
    private $ghostServices;

    /**
     * @var Resolver
     */
    private $resolver;

    protected function setUp()
    {
        $this->applicationMock = $this->getMock('Illuminate\Foundation\Application', [
            'bound', 'forceRegister', 'make',
            'getLoadedProviders', 'booting', 'bindShared',
            'bind'
        ], [], '', false);

        $this->ghostService = $this->getMockForAbstractClass(
            'LaravelCommode\Common\GhostService\GhostService',
            [$this->applicationMock],
            '',
            true,
            true,
            true,
            ['fakeMethodToResolve']
        );

        $this->commonGhostService = new CommodeCommonServiceProvider($this->applicationMock);

        $this->ghostServices = new GhostServices();
        $this->resolver = new Resolver($this->applicationMock);
    }

    public function testRegisterAbstract()
    {
        $boundWith = function () {
            return true;
        };

        $boundWill = function ($serviceName) {

            switch ($serviceName) {
                case 'commode.common.loaded':
                    return false;
            }

            dd('bound', $serviceName);

            return true;
        };

        $makeWill = function ($make) {
            switch ($make) {
                case 'commode.common.ghostservice':
                    return $this->ghostServices;
                    break;
                case 'commode.common.resolver':
                    return $this->resolver;
                    break;
            }

            dd('make', func_get_args());
        };

        $this->applicationMock->expects($this->any())
            ->method('getLoadedProviders')
            ->will($this->returnValue([]));

        $this->applicationMock->expects($this->any())
            ->method('bound')->with($this->callback($boundWith))
            ->will($this->returnCallback($boundWill));

        $this->applicationMock->expects($this->any())
            ->method('make')
            ->will($this->returnCallback($makeWill));

        $this->ghostService->register();
        $this->commonGhostService->register();
    }

    public function testLaunchClosure()
    {
        $this->ghostService->expects($this->any())
            ->method('resolving')
            ->will($this->returnValue(['fakeMethodToResolve']));


        $reflectionMethod = new \ReflectionMethod($this->ghostService, 'launchClosure');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($this->ghostService);
    }
}
