<?php

namespace LaravelCommode\Common;

use LaravelCommode\Common\Constants\ServiceShortCuts;
use PHPUnit_Framework_MockObject_MockObject as Mock;

class CommodeCommonServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Illuminate\Foundation\Application|Mock
     */
    private $applicationMock;

    /**
     * @var CommodeCommonServiceProvider
     */
    private $commodeService;

    protected function setUp()
    {
        $this->applicationMock = $this->getMock(
            'Illuminate\Foundation\Application',
            ['bindShared', 'bind', 'booting']
        );

        $this->commodeService = new CommodeCommonServiceProvider($this->applicationMock);

        parent::setUp();
    }

    public function testLaunching()
    {
        $launchingMethodReflection = new \ReflectionMethod($this->commodeService, 'launching');
        $launchingMethodReflection->setAccessible(true);
        $launchingMethodReflection->invoke($this->commodeService);
    }

    public function testProvides()
    {
        $this->assertSame(
            [ServiceShortCuts::GHOST_SERVICE, ServiceShortCuts::RESOLVER_SERVICE],
            $this->commodeService->provides()
        );
    }

    public function testRegistering()
    {
        $registeringMethodReflection = new \ReflectionMethod($this->commodeService, 'registering');
        $registeringMethodReflection->setAccessible(true);

        $this->applicationMock->expects($this->at(0))->method('bindShared')
            ->with($this->anything(), $this->callback(function ($closure) {
                $this->assertInstanceOf('LaravelCommode\Common\Resolver\Resolver', $closure());
                return true;
            }));

        $this->applicationMock->expects($this->at(1))->method('bindShared')
            ->with($this->anything(), $this->callback(function ($closure) {
                $this->assertInstanceOf('LaravelCommode\Common\GhostService\GhostServices', $closure());
                return true;
            }));

        $registeringMethodReflection->invoke($this->commodeService);
    }

    protected function tearDown()
    {
        unset($this->commodeService);
        unset($this->applicationMock);
        parent::tearDown();
    }
}
