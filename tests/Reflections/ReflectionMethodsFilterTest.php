<?php
    namespace Reflections;

    use LaravelCommode\Common\Reflections\ReflectionMethodsFilter;

    class TestCase
    {
        public function prefixFirstFunction()
        {

        }

        public function prefixSecondFunction()
        {

        }

        public function ThirdprefixWrong()
        {

        }
    }

    class ReflectionMethodsFilterTest extends \PHPUnit_Framework_TestCase
    {
        public function testFilterPrefix()
        {
            $expected = array('prefixFirstFunction', 'prefixSecondFunction');
            $reflection = new ReflectionMethodsFilter(new TestCase());

            $result = $reflection->filterPrefix('prefix');

            $this->assertSame($expected, $result);
        }
    }