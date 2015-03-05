<?php

class AdapterAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPExif\Adapter\Exiftool|\PHPExif\Adapter\Native
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = new \PHPExif\Adapter\Native();
    }

    /**
     * @group adapter
     * @covers \PHPExif\AdapterAbstract::determinePropertyGetter
     */
    public function testDeterminePropertyGetter()
    {
        $reflMethod = new \ReflectionMethod('\PHPExif\Adapter\Native', 'determinePropertyGetter');
        $reflMethod->setAccessible(true);

        $result = $reflMethod->invoke(
            $this->adapter,
            'foo'
        );

        $this->assertEquals('getFoo', $result);
    }

    /**
     * @group adapter
     * @covers \PHPExif\AdapterAbstract::determinePropertySetter
     */
    public function testDeterminePropertySetter()
    {
        $reflMethod = new \ReflectionMethod('\PHPExif\Adapter\Native', 'determinePropertySetter');
        $reflMethod->setAccessible(true);

        $result = $reflMethod->invoke(
            $this->adapter,
            'foo'
        );

        $this->assertEquals('setFoo', $result);
    }

    /**
     * @group adapter
     * @covers \PHPExif\AdapterAbstract::getClassConstantsOfType
     */
    public function testGetClassConstantsOfTypeAlwaysReturnsArray()
    {
        $result = $this->adapter->getClassConstantsOfType('sections');
        $this->assertInternalType('array', $result);
        $result = $this->adapter->getClassConstantsOfType('foo');
        $this->assertInternalType('array', $result);
    }

    /**
     * @group adapter
     * @covers \PHPExif\AdapterAbstract::getClassConstantsOfType
     */
    public function testGetClassConstantsOfTypeReturnsCorrectData()
    {
        $expected = array(
            'SECTIONS_AS_ARRAYS' => \PHPExif\Adapter\Native::SECTIONS_AS_ARRAYS,
            'SECTIONS_FLAT' => \PHPExif\Adapter\Native::SECTIONS_FLAT,
        );
        $actual = $this->adapter->getClassConstantsOfType('sections');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group adapter
     * @covers \PHPExif\AdapterAbstract::toArray
     */
    public function testToArrayReturnsPropertiesWithGetters()
    {
        $expected = array(
            'requiredSections',
            'includeThumbnail',
            'sectionsAsArrays',
        );
        $result = $this->adapter->toArray();
        $actual = array_keys($result);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group adapter
     * @covers \PHPExif\AdapterAbstract::toArray
     */
    public function testToArrayOmmitsPropertiesWithoutGetters()
    {
        $expected = array(
            'iptcMapping',
        );
        $result = $this->adapter->toArray();
        $actual = array_keys($result);
        $diff = array_diff($expected, $actual);
        $this->assertEquals($expected, $diff);
    }

    /**
     * @group adapter
     * @covers \PHPExif\AdapterAbstract::setOptions
     */
    public function testSetOptionsReturnsCurrentInstance()
    {
        $result = $this->adapter->setOptions(array());
        $this->assertSame($this->adapter, $result);
    }

    /**
     * @group adapter
     * @covers \PHPExif\AdapterAbstract::setOptions
     */
    public function testSetOptionsCorrectlySetsProperties()
    {
        $expected = array(
            'requiredSections'  => array('foo', 'bar', 'baz',),
            'includeThumbnail' => \PHPExif\Adapter\Native::INCLUDE_THUMBNAIL,
            'sectionsAsArrays' => \PHPExif\Adapter\Native::SECTIONS_AS_ARRAYS,
        );
        $this->adapter->setOptions($expected);

        foreach ($expected as $key => $value) {
            $reflProp = new \ReflectionProperty('\PHPExif\Adapter\Native', $key);
            $reflProp->setAccessible(true);
            $this->assertEquals($value, $reflProp->getValue($this->adapter));
        }
    }

    /**
     * @group adapter
     * @covers \PHPExif\AdapterAbstract::setOptions
     */
    public function testSetOptionsIgnoresPropertiesWithoutSetters()
    {
        $expected = array(
            'iptcMapping' => array('foo', 'bar', 'baz'),
        );
        $this->adapter->setOptions($expected);

        foreach ($expected as $key => $value) {
            $reflProp = new \ReflectionProperty('\PHPExif\Adapter\Native', $key);
            $reflProp->setAccessible(true);
            $this->assertNotEquals($value, $reflProp->getValue($this->adapter));
        }
    }


    /**
     * @group adapter
     * @covers \PHPExif\AdapterAbstract::__construct
     */
    public function testConstructorSetsOptions()
    {
        $expected = array(
            'requiredSections'  => array('foo', 'bar', 'baz',),
            'includeThumbnail' => \PHPExif\Adapter\Native::INCLUDE_THUMBNAIL,
            'sectionsAsArrays' => \PHPExif\Adapter\Native::SECTIONS_AS_ARRAYS,
        );
        $adapter = new \PHPExif\Adapter\Native($expected);

        foreach ($expected as $key => $value) {
            $reflProp = new \ReflectionProperty('\PHPExif\Adapter\Native', $key);
            $reflProp->setAccessible(true);
            $this->assertEquals($value, $reflProp->getValue($adapter));
        }
    }
}
