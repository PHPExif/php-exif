<?php

use PHPExif\Adapter\AbstractAdapter;
use PHPExif\Adapter\Exiftool;
use PHPExif\Adapter\Native;

class AbstractAdapterTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var Exiftool|Native
     */
    protected Exiftool|Native $adapter;

    protected function setUp(): void
    {
        $this->adapter = new Native();
    }

    /**
     * @group adapter
     */
    public function testSetOptionsReturnsCurrentInstance()
    {
        $result = $this->adapter->setOptions(array());
        $this->assertSame($this->adapter, $result);
    }

    /**
     * @group adapter
     */
    public function testSetOptionsCorrectlySetsProperties()
    {
        $expected = array(
            'requiredSections'  => array('foo', 'bar', 'baz',),
            'includeThumbnail' => Native::INCLUDE_THUMBNAIL,
            'sectionsAsArrays' => Native::SECTIONS_AS_ARRAYS,
        );
        $this->adapter->setOptions($expected);

        foreach ($expected as $key => $value) {
            $reflProp = new \ReflectionProperty(Native::class, $key);
            $reflProp->setAccessible(true);
            $this->assertEquals($value, $reflProp->getValue($this->adapter));
        }
    }

    /**
     * @group adapter
     */
    public function testSetOptionsIgnoresPropertiesWithoutSetters()
    {
        $expected = array(
            'iptcMapping' => array('foo', 'bar', 'baz'),
        );
        $this->adapter->setOptions($expected);

        foreach ($expected as $key => $value) {
            $reflProp = new \ReflectionProperty(Native::class, $key);
            $reflProp->setAccessible(true);
            $this->assertNotEquals($value, $reflProp->getValue($this->adapter));
        }
    }


    /**
     * @group adapter
     */
    public function testConstructorSetsOptions()
    {
        $expected = array(
            'requiredSections'  => array('foo', 'bar', 'baz',),
            'includeThumbnail' => Native::INCLUDE_THUMBNAIL,
            'sectionsAsArrays' => Native::SECTIONS_AS_ARRAYS,
        );
        $adapter = new Native($expected);

        foreach ($expected as $key => $value) {
            $reflProp = new \ReflectionProperty(Native::class, $key);
            $reflProp->setAccessible(true);
            $this->assertEquals($value, $reflProp->getValue($adapter));
        }
    }

    /**
     * @group adapter
     */
    public function testSetMapperReturnsCurrentInstance()
    {
        $mapper = new \PHPExif\Mapper\Native();
        $result = $this->adapter->setMapper($mapper);
        $this->assertSame($this->adapter, $result);
    }

    /**
     * @group adapter
     */
    public function testSetMapperCorrectlySetsInProperty()
    {
        $mapper = new \PHPExif\Mapper\Native();
        $this->adapter->setMapper($mapper);

        $reflProp = new \ReflectionProperty(AbstractAdapter::class, 'mapper');
        $reflProp->setAccessible(true);
        $this->assertSame($mapper, $reflProp->getValue($this->adapter));
    }

    /**
     * @group adapter
     */
    public function testGetMapperCorrectlyReturnsFromProperty()
    {
        $mapper = new \PHPExif\Mapper\Native();
        $reflProp = new \ReflectionProperty(AbstractAdapter::class, 'mapper');
        $reflProp->setAccessible(true);
        $reflProp->setValue($this->adapter, $mapper);
        $this->assertSame($mapper, $this->adapter->getMapper());
    }

    /**
     * @group adapter
     */
    public function testGetMapperLazyLoadsMapperWhenNotPresent()
    {
        $reflProp = new \ReflectionProperty(
            get_class($this->adapter),
            'mapperClass'
        );

        $mapperClass = '\\PHPExif\\Mapper\\Native';
        $reflProp->setAccessible(true);
        $reflProp->setValue($this->adapter, $mapperClass);

        $this->assertInstanceOf($mapperClass, $this->adapter->getMapper());
    }

    /**
     * @group adapter
     */
    public function testGetMapperLazyLoadingSetsInProperty()
    {
        $reflProp = new \ReflectionProperty(
            get_class($this->adapter),
            'mapperClass'
        );

        $mapperClass = '\\PHPExif\\Mapper\\Native';
        $reflProp->setAccessible(true);
        $reflProp->setValue($this->adapter, $mapperClass);

        $reflProp2 = new \ReflectionProperty(
            get_class($this->adapter),
            'mapper'
        );
        $reflProp2->setAccessible(true);
        $this->adapter->getMapper();
        $this->assertInstanceOf($mapperClass, $reflProp2->getValue($this->adapter));
    }

    /**
     * @group adapter
     */
    public function testSetHydratorReturnsCurrentInstance()
    {
        $hydrator = new \PHPExif\Hydrator\Mutator();
        $result = $this->adapter->setHydrator($hydrator);
        $this->assertSame($this->adapter, $result);
    }

    /**
     * @group adapter
     */
    public function testSetHydratorCorrectlySetsInProperty()
    {
        $hydrator = new \PHPExif\Hydrator\Mutator();
        $this->adapter->setHydrator($hydrator);

        $reflProp = new \ReflectionProperty(AbstractAdapter::class, 'hydrator');
        $reflProp->setAccessible(true);
        $this->assertSame($hydrator, $reflProp->getValue($this->adapter));
    }

    /**
     * @group adapter
     */
    public function testGetHydratorCorrectlyReturnsFromProperty()
    {
        $hydrator = new \PHPExif\Hydrator\Mutator();
        $reflProp = new \ReflectionProperty(AbstractAdapter::class, 'hydrator');
        $reflProp->setAccessible(true);
        $reflProp->setValue($this->adapter, $hydrator);
        $this->assertSame($hydrator, $this->adapter->getHydrator());
    }

    /**
     * @group adapter
     */
    public function testGetHydratorLazyLoadsHydratorWhenNotPresent()
    {
        $hydratorClass = '\\PHPExif\\Hydrator\\Mutator';
        $this->assertInstanceOf($hydratorClass, $this->adapter->getHydrator());
    }

    /**
     * @group adapter
     */
    public function testGetHydratorLazyLoadingSetsInProperty()
    {
        $hydratorClass = '\\PHPExif\\Hydrator\\Mutator';

        $reflProp = new \ReflectionProperty(
            get_class($this->adapter),
            'hydrator'
        );
        $reflProp->setAccessible(true);
        $this->adapter->getHydrator();
        $this->assertInstanceOf($hydratorClass, $reflProp->getValue($this->adapter));
    }
}
