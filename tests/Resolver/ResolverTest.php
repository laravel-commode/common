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
            $resolver = new Resolver($appMock = $this->getAppMock());
            $resolvedClass = new ResolvingClass();

            $parameters = [1];
            $expectedResolverParameters = array_merge($parameters, [$resolver]);

            $appMock->shouldReceive('bound')->zeroOrMoreTimes()->andReturn(true);
            $appMock->shouldReceive('make')->times(1)->andReturn($resolver);


            $result = $resolver->method($resolvedClass, 'resolvingMethod', $parameters);

            $this->assertNotSameSize($parameters, $result);
            $this->assertSame($expectedResolverParameters, $result);
        }

        public function testResolverMethodString()
        {
            $resolver = new Resolver($appMock = $this->getAppMock());
            $resolvedClass = new ResolvingClass();

            $parameters = [1];
            $expectedResolverParameters = array_merge($parameters, [$resolver]);

            $appMock->shouldReceive('bound')->zeroOrMoreTimes()->andReturn(true);
            $appMock->shouldReceive('make')->times(2)->andReturn($resolvedClass, $resolver);

            $result = $resolver->method('Resolver\ResolvingClass', 'resolvingMethod', $parameters);

            $this->assertNotSameSize($parameters, $result);
            $this->assertSame($expectedResolverParameters, $result);
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

        public function testResolverMethodToClosureString()
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
    } 