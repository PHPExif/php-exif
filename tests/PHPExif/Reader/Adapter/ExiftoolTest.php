<?php
class ExiftoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPExif\Reader\Adapter\Exiftool
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = new \PHPExif\Reader\Adapter\Exiftool();
    }

    /**
     * @group exiftool
     * @covers \PHPExif\Reader\Adapter\Exiftool::getToolPath
     */
    public function testGetToolPathFromProperty()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Adapter\Exiftool', 'toolPath');
        $reflProperty->setAccessible(true);
        $expected = '/foo/bar/baz';
        $reflProperty->setValue($this->adapter, $expected);

        $this->assertEquals($expected, $this->adapter->getToolPath());
    }

    /**
     * @group exiftool
     * @covers \PHPExif\Reader\Adapter\Exiftool::setToolPath
     */
    public function testSetToolPathInProperty()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Adapter\Exiftool', 'toolPath');
        $reflProperty->setAccessible(true);

        $expected = '/tmp';
        $this->adapter->setToolPath($expected);

        $this->assertEquals($expected, $reflProperty->getValue($this->adapter));
    }

    /**
     * @group exiftool
     * @covers \PHPExif\Reader\Adapter\Exiftool::setToolPath
     * @expectedException InvalidArgumentException
     */
    public function testSetToolPathThrowsException()
    {
        $this->adapter->setToolPath('/foo/bar');
    }


    /**
     * @group exiftool
     * @covers \PHPExif\Reader\Adapter\Exiftool::getToolPath
     */
    public function testGetToolPathLazyLoadsPath()
    {
        $this->assertInternalType('string', $this->adapter->getToolPath());
    }

    /**
     * @group exiftool
     * @covers \PHPExif\Reader\Adapter\Exiftool::getExifFromFile
     */
    public function testGetExifFromFile()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/morning_glory_pool_500.jpg';
        $result = $this->adapter->getExifFromFile($file);
        $this->assertInstanceOf('\PHPExif\Exif', $result);
    }

    /**
     * @group exiftool
     * @covers \PHPExif\Reader\Adapter\Exiftool::mapData
     */
    public function testMapDataReturnsArray()
    {
        $this->assertInternalType('array', $this->adapter->mapData(array()));
    }

    /**
     * @group exiftool
     * @covers \PHPExif\Reader\Adapter\Exiftool::mapData
     */
    public function testMapDataReturnsArrayFalseValuesIfUndefined()
    {
        $result = $this->adapter->mapData(array());

        foreach ($result as $value) {
            $this->assertFalse($value);
        }
    }

    /**
     * @group exiftool
     * @covers \PHPExif\Reader\Adapter\Exiftool::mapData
     */
    public function testMapDataResultHasAllKeys()
    {
        $reflClass = new \ReflectionClass('\PHPExif\Exif');
        $constants = $reflClass->getConstants();
        $result = $this->adapter->mapData(array());
        $keys = array_keys($result);

        $diff = array_diff($constants, $keys);

        $this->assertEquals(0, count($diff));
    }

    /**
     * @group exiftool
     * @covers \PHPExif\Reader\Adapter\Exiftool::mapData
     */
    public function testMapDataFocalLengthIsCalculated()
    {
        $focalLength =  '18 mm.';

        $result = $this->adapter->mapData(
            array(
                'FocalLength' => $focalLength,
            )
        );

        $this->assertEquals(18, $result[\PHPExif\Exif::FOCAL_LENGTH]);
    }

    /**
     * @group exiftool
     * @covers \PHPExif\Reader\Adapter\Exiftool::getCliOutput
     */
    public function testGetCliOutput()
    {
        $reflMethod = new \ReflectionMethod('\PHPExif\Reader\Adapter\Exiftool', 'getCliOutput');
        $reflMethod->setAccessible(true);

        $result = $reflMethod->invoke(
            $this->adapter,
            sprintf(
                '%1$s',
                'pwd'
            )
        );

        $this->assertInternalType('string', $result);
    }
}
