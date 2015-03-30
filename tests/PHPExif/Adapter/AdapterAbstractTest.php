<?php
/**
 * @covers \PHPExif\Adapter\AdapterAbstract::<!public>
 */
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
     * @covers \PHPExif\Adapter\AdapterAbstract::setOptions
     */
    public function testSetOptionsReturnsCurrentInstance()
    {
        $result = $this->adapter->setOptions(array());
        $this->assertSame($this->adapter, $result);
    }

    /**
     * @group adapter
     * @covers \PHPExif\Adapter\AdapterAbstract::setOptions
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
            $reflProp = new \ReflectionProperty('\\PHPExif\\Adapter\\Native', $key);
            $reflProp->setAccessible(true);
            $this->assertEquals($value, $reflProp->getValue($this->adapter));
        }
    }

    /**
     * @group adapter
     * @covers \PHPExif\Adapter\AdapterAbstract::setOptions
     */
    public function testSetOptionsIgnoresPropertiesWithoutSetters()
    {
        $expected = array(
            'iptcMapping' => array('foo', 'bar', 'baz'),
        );
        $this->adapter->setOptions($expected);

        foreach ($expected as $key => $value) {
            $reflProp = new \ReflectionProperty('\\PHPExif\\Adapter\\Native', $key);
            $reflProp->setAccessible(true);
            $this->assertNotEquals($value, $reflProp->getValue($this->adapter));
        }
    }


    /**
     * @group adapter
     * @covers \PHPExif\Adapter\AdapterAbstract::__construct
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
            $reflProp = new \ReflectionProperty('\\PHPExif\\Adapter\\Native', $key);
            $reflProp->setAccessible(true);
            $this->assertEquals($value, $reflProp->getValue($adapter));
        }
    }
}
