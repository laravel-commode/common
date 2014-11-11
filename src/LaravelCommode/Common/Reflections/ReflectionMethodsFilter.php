<?php
    namespace LaravelCommode\Common\Reflections;

    use ReflectionClass;

    class ReflectionMethodsFilter extends ReflectionClass
    {
        public function filterPrefix($prefix)
        {
            $names = [];

            foreach($this->getMethods() as $method)
            {
                if ($method->isPublic() && substr_count($method->getName(), $prefix))
                {
                    $names[] = $method->getName();
                }
            }

            return $names;
        }


    }