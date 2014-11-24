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
            $abstractRegistryMock = $this->getMockBuilder('LaravelCommode\Common\Registry\AbstractRegistry')->getMockForAbstractClass();
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

        public function testGetSet()
        {
            $mock = $this->buildARMock();

            $mock['setAccess'] = 5;
            $this->assertSame($mock['setAccess'], 5);

            $mock->offsetSet('setMethod', 5);
            $this->assertSame($mock['setMethod'], 5);
        }

        public function testUnset()
        {
            $mock = $this->buildARMock();

            $mock['setAccess'] = 5;

            $this->assertTrue($mock->offsetExists('setAccess'));

            unset($mock['setAccess']);

            $this->assertFalse($mock->offsetExists('setAccess'));
        }

        public function testNext()
        {
            $mock = $this->buildARMock();

            $mock['one'] = 5;
            $mock['two'] = 6;

            reset($mock);
            $mock->next();

            $this->assertSame($mock->current(), 6);
        }

        public function testKey()
        {
            $mock = $this->buildARMock();

            $mock['one'] = 5;
            $mock['two'] = 6;

            $this->assertSame($mock->key(), 'one');
            $mock->next();
            $this->assertSame($mock->key(), 'two');
        }

        public function testValid()
        {
            $mock = $this->buildARMock();

            $this->assertSame(!is_null($mock->key()), $mock->valid());

            $mock[0] = 0;

            $this->assertNotSame(is_null($mock->key()), $mock->valid());
        }

        public function testRewind()
        {
            $mock = $this->buildARMock();

            $mock[0] = 0;
            $mock[1] = 0;
            $mock[2] = 0;
            $mock[3] = 0;

            $mock->next();
            $mock->next();
            $mock->next();

            $this->assertSame($mock->key(), 3);

            $mock->rewind();

            $this->assertNotSame($mock->key(), 3);
            $this->assertSame($mock->key(), 0);
        }

        public function testCurrent()
        {
            $mock = $this->buildARMock();

            $mock['setAccess'] = 5;

            $this->assertSame($mock->current(), 5);
        }


        public function testOffsetExists()
        {
            $mock = $this->buildARMock();

            $mock['set'] = 5;

            $this->assertTrue(isset($mock['set']));

            $this->assertSame(isset($mock['set']), $mock->offsetExists('set'));
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