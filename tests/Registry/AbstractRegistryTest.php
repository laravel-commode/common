<?php
    namespace Registry;
    use LaravelCommode\Common\Registry\AbstractRegistry;
    use Mockery;

    /**
 * Created by PhpStorm.
 * User: madman
 * Date: 11/23/14
 * Time: 11:39 PM
 */
    class AbstractRegistryTest extends \PHPUnit_Framework_TestCase
    {
        protected function buildARMock()
        {
            $abstractRegistryMock = $this->getMockBuilder(AbstractRegistry::class)->getMockForAbstractClass();
            $abstractRegistryMockExpectation = $abstractRegistryMock->expects($this->any());

            $abstractRegistryMockExpectation->method('getContainerName')->will(
                $this->returnValue('container')
            );

            $abstractRegistryMock->container = [];

            return $abstractRegistryMock;
        }

        public function testMerge()
        {
            $testArray = [1,2,3,4];
            $mergeArray = [5,6,7,8];

            $mock = $this->buildARMock();

            foreach($testArray as $value)
            {
                $mock[] = $value;
            }

            $mergedResult = $mock->merge($mergeArray)->toArray();

            $this->assertSame($mergedResult, array_merge($testArray, $mergeArray));
            $this->assertSameSize($mergedResult, array_merge($testArray, $mergeArray));
        }

        public function testMergeRegistry()
        {
            $testArray = [1,2,3,4];
            $mergeArray = [5,6,7,8];

            $mock = $this->buildARMock();
            $mockMerged = $this->buildARMock();

            foreach($testArray as $value)
            {
                $mock[] = $value;
            }

            foreach($mergeArray as $value)
            {
                $mockMerged[] = $value;
            }

            $mock->mergeRegistry($mockMerged);

            $this->assertSame($mock->toArray(), array_merge($testArray, $mergeArray));
            $this->assertSameSize($mock->toArray(), array_merge($testArray, $mergeArray));
        }

        public function testToArray()
        {
            $testArray = [1,2,3,4];
            $mock = $this->buildARMock();

            foreach($testArray as $value)
            {
                $mock[] = $value;
            }

            $this->assertSame($mock->toArray(), $testArray);
            $this->assertSameSize($mock->toArray(), $testArray);
        }
    } 