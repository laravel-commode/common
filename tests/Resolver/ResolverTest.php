<?php
    namespace Resolver;
    use Closure;
    use LaravelCommode\Common\Resolver\Resolver;
    use Mockery as M;

    class ResolvingClass
    {
        public function resolvingMethod($int, Resolver $resolver = null)
        {
            return func_get_args();
        }

        public function resolvingDummyFail($int, Dummy $dummy = null)
        {
            return func_get_args();
        }

        private function resolvingScope($int, Resolver $resolver = null)
        {
            return $this->resolvingMethod($int, $resolver);
        }
    }

    class ResolverTest extends \PHPUnit_Framework_TestCase
    {
        protected function getAppMock()
        {
            return M::mock('Illuminate\Foundation\Application');
        }

        public function tearDown()
        {
            M::close();
        }

        protected function buildForMethodResolving()
        {
            $resolver = new Resolver($appMock = $this->getAppMock());

            $appMock->shouldReceive('bound')->zeroOrMoreTimes()->andReturn(true);
            $appMock->shouldReceive('make')->times(1)->andReturn($resolver);

            return $resolver;
        }

        public function testResolverClosure()
        {
            $appMock = $this->getAppMock();

            $resolver = new Resolver($appMock);

            $appMock->shouldReceive('bound')->zeroOrMoreTimes()->andReturn(true);
            $appMock->shouldReceive('make')->times(1)->andReturn($resolver);

            $parameters = [1];
            $expectedResolverParameters = array_merge($parameters, [$resolver]);

            $closure = function($int, Resolver $resolver = null)
            {
                return func_get_args();
            };

            $result = $resolver->closure($closure, $parameters);

            $this->assertNotSameSize($parameters, $result);
            $this->assertSame($expectedResolverParameters, $result);
        }

        public function testResolverMakeClosure()
        {
            $appMock = $this->getAppMock();

            $resolver = new Resolver($appMock);

            $appMock->shouldReceive('bound')->zeroOrMoreTimes()->andReturn(true);
            $appMock->shouldReceive('make')->times(1)->andReturn($resolver);

            $parameters = [1];
            $expectedResolverParameters = array_merge($parameters, [$resolver]);

            $closure = function($int, Resolver $resolver = null)
            {
                return func_get_args();
            };

            $resultClosure = $resolver->makeClosure($closure);
            $resultResult = $resultClosure(1);

            $this->assertTrue($resultClosure instanceof Closure);

            $this->assertNotSameSize($resultResult, $parameters);
            $this->assertNotSame($resultResult, $parameters);

            $this->assertSameSize($resultResult, $expectedResolverParameters);
            $this->assertSame($resultResult, $expectedResolverParameters);
        }

        public function testResolverMethodInstance()
        {
            $resolver = $this->buildForMethodResolving();
            $resolvedClass = new ResolvingClass();

            $parameters = [1];
            $expectedResolverParameters = array_merge($parameters, [$resolver]);

            $result = $resolver->method($resolvedClass, 'resolvingMethod', $parameters);

            $this->assertNotSameSize($parameters, $result);
            $this->assertSame($expectedResolverParameters, $result);
        }

        public function testResolverMethodInstanceScope()
        {
            $resolver = $this->buildForMethodResolving();
            $resolvedClass = new ResolvingClass();

            $parameters = ['some value'];
            $expectedResolverParameters = array_merge($parameters, [$resolver]);

            $result = $resolver->method($resolvedClass, 'resolvingScope', $parameters, true);

            $this->assertNotSameSize($parameters, $result);
            $this->assertSame($expectedResolverParameters, $result);
        }

        public function testUnableToResolveMethod()
        {
            $resolver = new Resolver($appMock = $this->getAppMock());
            $resolvedClass = new ResolvingClass();

            $parameters = [1];

            $appMock->shouldReceive('bound')->zeroOrMoreTimes()->andReturn(false);


            $result = $resolver->method($resolvedClass, 'resolvingDummyFail', $parameters);

            $this->assertSame($result, $parameters);

        }

        public function testUnableToResolveClosure()
        {
            $resolver = new Resolver($appMock = $this->getAppMock());

            $resolvedClosure = function($int, Dummy $dummy = null)
            {
                return func_get_args();
            };

            $parameters = [1];

            $appMock->shouldReceive('bound')->zeroOrMoreTimes()->andReturn(false);

            $result = $resolver->closure($resolvedClosure, $parameters);

            $this->assertSame($result, $parameters);
        }

        public function testResolverMethodInstanceScopeException()
        {
            $resolver = $this->buildForMethodResolving();
            $resolvedClass = new ResolvingClass();

            $parameters = [1];
            $expectedResolverParameters = array_merge($parameters, [$resolver]);

            try {
                $result = $resolver->method($resolvedClass, 'resolvingScope', $parameters);
            } catch(\Exception $e) {
                $this->assertSame(
                    'call_user_func_array() expects parameter 1 to be a valid callback, cannot access private method Resolver\ResolvingClass::resolvingScope()',
                    $e->getMessage()
                );
            }
        }

        public function testResolverMethodToClosureInstance()
        {
            $resolver = new Resolver($appMock = $this->getAppMock());
            $resolvedClass = new ResolvingClass();

            $parameters = [1];
            $expectedResolverParameters = array_merge($parameters, [$resolver]);

            $appMock->shouldReceive('bound')->zeroOrMoreTimes()->andReturn(true);
            $appMock->shouldReceive('make')->times(1)->andReturn($resolver);


            $resultClosure = $resolver->methodToClosure($resolvedClass, 'resolvingMethod');
            $this->assertTrue($resultClosure instanceof Closure);

            $result = $resultClosure($parameters[0]);

            $this->assertNotSameSize($parameters, $result);
            $this->assertSame($expectedResolverParameters, $result);
        }

        public function testResolveMethodParameters()
        {
            $resolver = new Resolver($appMock = $this->getAppMock());

            $appMock->shouldReceive('bound')->zeroOrMoreTimes()->andReturn(true);
            $appMock->shouldReceive('make')->times(1)->andReturn($resolver);

            $resolvedClass = new ResolvingClass();

            $parameters = [1];
            $expectedResolverParameters = array_merge($parameters, [$resolver]);

            $result = $resolver->resolveMethodParameters($resolvedClass, 'resolvingMethod', $parameters);

            $this->assertNotSameSize($parameters, $result);
            $this->assertSame($expectedResolverParameters, $result);
        }
    } 