<?php

namespace {

    // This allow us to configure the behavior of the "global mock"
    $mockTrans = true;
}

namespace LaravelCommode\Common\Meta\LocalizedMeta {

    function trans($id, array $parameters = [], $domain = 'messages', $locale = null)
    {
        global $mockTrans;

        if ($mockTrans !== null && $mockTrans === true) {
            $parameters = [MetaDataTest::TEST_TRANS => MetaDataTest::TEST_TRANS_VALUE];
            return array_key_exists($id, $parameters) ?  $parameters[$id] : $id;
        } else {
            return call_user_func_array('\trans', func_get_args());
        }
    }



    class MetaDataTest extends \PHPUnit_Framework_TestCase
    {
        const TEST_TRANS = 'look.value';
        const TEST_TRANS_VALUE = 'valued';
        /**
         * @param array $arguments
         * @return \PHPUnit_Framework_MockObject_MockObject|MetaData
         */
        protected function getInstance(array $arguments = [])
        {
            return $this->getMockForAbstractClass(
                'LaravelCommode\Common\Meta\LocalizedMeta\MetaData',
                $arguments
            );

        }

        public function testConstructor()
        {
            $class = $this->getInstance(['ru']);
            $somethingRu = uniqid('ru_');
            $class->ru_something = $somethingRu;
            $this->assertEquals($somethingRu, $class->something);

            $class = $this->getInstance(['en']);
            $somethingEn = uniqid('en_');
            $class->en_something = $somethingEn;
            $this->assertEquals($somethingEn, $class->something);


        }

        public function testGetSetLocale()
        {
            $arguments = ['en'];
            $class = $this->getInstance(['en']);

            $this->assertEquals($arguments[0], $class->getLocale());

            $locale = 'ru';

            $class->setLocale($locale);

            $this->assertEquals($locale, $class->getLocale());
        }

        public function testLookUp()
        {
            $class = $this->getInstance(['en', 'look']);

            $this->assertEquals(self::TEST_TRANS_VALUE, $class->value);
            $this->assertNotEquals(self::TEST_TRANS_VALUE, $class->values);
            $this->assertEquals("values", $class->values);
        }

        public function testGetSetLookUpLocation()
        {
            $arguments = ['en', 'custom'];
            $class = $this->getInstance(['en', 'custom']);

            $this->assertEquals($arguments[1], $class->getLookUpLocation());

            $lookUpLocation = 'otherCustom';

            $class->setLookUpLocation($lookUpLocation);

            $this->assertEquals($lookUpLocation, $class->getLookUpLocation());
        }
    }
}
