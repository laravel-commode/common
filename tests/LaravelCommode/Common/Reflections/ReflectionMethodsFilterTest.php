<?php

namespace Reflections;

use LaravelCommode\Common\Reflections\ReflectionMethodsFilter;

class ReflectionMethodsFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterPrefix()
    {
        $expected = ['prefixFirstFunction', 'prefixSecondFunction'];
        $reflection = new ReflectionMethodsFilter(new ReflectionMethodsFilterTestSubject());

        $result = $reflection->filterPrefix('prefix');

        $this->assertSame($expected, $result);
    }
}