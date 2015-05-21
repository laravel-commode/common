<?php

namespace LaravelCommode\Common\Controllers;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use LaravelCommode\Common\Resolver\Resolver;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use ReflectionProperty;

class CommodeControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CommodeControllerTestSubject
     */
    private $controller;

    /**
     * @var Mock|Application
     */
    private $applicationMock;

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var Request|Mock
     */
    private $requestMock;

    protected function setUp()
    {
        $this->controller = new CommodeControllerTestSubject();

        $this->applicationMock = $this->getMock('Illuminate\Foundation\Application', ['make']);

        $this->requestMock = $this->getMock('Illuminate\Http\Request', ['ajax']);
        $this->resolver = new Resolver($this->applicationMock);

        Facade::setFacadeApplication($this->applicationMock);

        parent::setUp();
    }


    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testMethodCalls()
    {
        $id = uniqid('testMethodCallId');

        $pass = [$id];
        $expect = [$id, $this];

        $this->applicationMock->expects($this->any())->method('make')
            ->will($this->returnCallback(function ($make) {
                switch ($make)
                {
                    case 'request':
                        return $this->requestMock;
                    case 'commode.common.resolver':
                        return $this->resolver;
                    case 'LaravelCommode\Common\Controllers\CommodeControllerTest':
                        return $this;
                }

                dd(func_get_args());
            }));

        $this->requestMock->expects($this->at(0))->method('ajax')
            ->will($this->returnValue(false));
        $this->requestMock->expects($this->at(1))->method('ajax')
            ->will($this->returnValue(false));
        $this->requestMock->expects($this->at(2))->method('ajax')
            ->will($this->returnValue(true));

        $resolveMethodsReflection = new ReflectionProperty($this->controller, 'resolveMethods');
        $resolveMethodsReflection->setAccessible(true);

        $resolveMethodsReflection->setValue($this->controller, false);
        $this->assertSame($pass, $this->controller->callAction('getSomeMethod', $pass));

        $resolveMethodsReflection->setValue($this->controller, true);
        $this->assertSame($expect, $this->controller->callAction('getSomeMethodResolve', $pass));

        $separateRequestsReflection = new ReflectionProperty($this->controller, 'separateRequests');
        $separateRequestsReflection->setAccessible(true);
        $separateRequestsReflection->setValue($this->controller, true);

        $this->requestMock->expects($this->any())->method('ajax')
            ->will($this->returnValue(true));

        $this->assertSame($expect, $this->controller->callAction('getSomeMethodResolve', $pass));
    }
}
