<?php

namespace LaravelCommode\Common\Reflections;

use ReflectionClass;

/**
 * Class ReflectionMethodsFilter
 *
 * Is an extended ReflectionClass that can
 * filter public methods by it's prefix.
 *
 * @author Volynov Andrey
 * @package LaravelCommode\Common\Reflections
 */
class ReflectionMethodsFilter extends ReflectionClass
{
    /**
     * Returns array of strings which contain public methods' names
     * which are prefixed with $prefix.
     *
     * @param string $prefix Method prefix.
     * @return string[]
     */
    public function filterPrefix($prefix)
    {
        $names = [];

        foreach ($this->getMethods() as $method) {
            if ($method->isPublic() && substr_count($method->getName(), $prefix, null, strlen($prefix))) {
                $names[] = $method->getName();
            }
        }

        return $names;
    }
}
