<?php
/**
 * @covers \PHPExif\Mapper\Native::<!public>
 */
class NativeMapperTest extends \PHPUnit_Framework_TestCase
{
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new \PHPExif\Mapper\Native;
    }

    /**
     * @group mapper
     */
    public function testClassImplementsCorrectInterface()
    {
        $this->assertInstanceOf('\\PHPExif\\Mapper\\MapperInterface', $this->mapper);
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::mapRawData
     */
    public function testMapRawDataIgnoresFieldIfItDoesntExist()
    {
        $rawData = array('foo' => 'bar');
        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertCount(0, $mapped);
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::mapRawData
     */
    public function testMapRawDataMapsFieldsCorrectly()
    {
        $reflProp = new \ReflectionProperty(get_class($this->mapper), 'map');
        $reflProp->setAccessible(true);
        $map = $reflProp->getValue($this->mapper);

        // ignore custom formatted data stuff:
        unset($map[\PHPExif\Mapper\Native::DATETIMEORIGINAL]);
        unset($map[\PHPExif\Mapper\Native::EXPOSURETIME]);
        unset($map[\PHPExif\Mapper\Native::FOCALLENGTH]);
        unset($map[\PHPExif\Mapper\Native::XRESOLUTION]);
        unset($map[\PHPExif\Mapper\Native::YRESOLUTION]);
        unset($map[\PHPExif\Mapper\Native::GPSLATITUDE]);
        unset($map[\PHPExif\Mapper\Native::GPSLONGITUDE]);

        // create raw data
        $keys = array_keys($map);
        $values = array();
        $values = array_pad($values, count($keys), 'foo');
        $rawData = array_combine($keys, $values);


        $mapped = $this->mapper->mapRawData($rawData);

        $i = 0;
        foreach ($mapped as $key => $value) {
            $this->assertEquals($map[$keys[$i]], $key);
            $i++;
        }
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsDateTimeOriginal()
    {
        $rawData = array(
            \PHPExif\Mapper\Native::DATETIMEORIGINAL => '2015:04:01 12:11:09',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $result = reset($mapped);
        $this->assertInstanceOf('\\DateTime', $result);
        $this->assertEquals(
            reset($rawData),
            $result->format('Y:m:d H:i:s')
        );
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsExposureTime()
    {
        $rawData = array(
            \PHPExif\Mapper\Native::EXPOSURETIME => '2/800',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals('1/400', reset($mapped));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsFocalLength()
    {
        $rawData = array(
            \PHPExif\Mapper\Native::FOCALLENGTH => '30/5',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(6, reset($mapped));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsXResolution()
    {
        $rawData = array(
            \PHPExif\Mapper\Native::XRESOLUTION => '1500/300',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(1500, reset($mapped));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsYResolution()
    {
        $rawData = array(
            \PHPExif\Mapper\Native::YRESOLUTION => '1500/300',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(1500, reset($mapped));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::mapRawData
     */
    public function testMapRawDataFlattensRawDataWithSections()
    {
        $rawData = array(
            \PHPExif\Mapper\Native::SECTION_COMPUTED => array(
                \PHPExif\Mapper\Native::TITLE => 'Hello',
            ),
            \PHPExif\Mapper\Native::HEADLINE => 'Headline',
        );
        $mapped = $this->mapper->mapRawData($rawData);
        $this->assertCount(2, $mapped);
        $keys = array_keys($mapped);

        $expected = array(
            \PHPExif\Mapper\Native::TITLE,
            \PHPExif\Mapper\Native::HEADLINE
        );
        $this->assertEquals($expected, $keys);
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsGPSData()
    {
        $expected = array(
            '40.333452380952,-20.167314814815' => array(
                'GPSLatitude'     => array('40/1', '20/1', '15/35'),
                'GPSLatitudeRef'  => 'N',
                'GPSLongitude'    => array('20/1', '10/1', '35/15'),
                'GPSLongitudeRef' => 'W',
            ),
            '0,-0' => array(
                'GPSLatitude'     => array('0/0', '0/0', '0/0'),
                'GPSLatitudeRef'  => 'N',
                'GPSLongitude'    => array('0/0', '0/0', '0/0'),
                'GPSLongitudeRef' => 'W',
            ),
        );

        foreach ($expected as $key => $value) {
            $result = $this->mapper->mapRawData($value);
            $this->assertEquals($key, reset($result));
        }
    }

    public function testMapRawDataCorrectlyFormatsDifferentDateTimeString()
    {
        $rawData = array(
            \PHPExif\Mapper\Native::DATETIMEORIGINAL => '2014-12-15 00:12:00'
        );

        $mapped = $this->mapper->mapRawData(
            $rawData
        );

        $result = reset($mapped);
        $this->assertInstanceOf('\DateTime', $result);
        $this->assertEquals(
            reset($rawData),
            $result->format("Y-m-d H:i:s")
        );
    }

    public function testMapRawDataCorrectlyIgnoresInvalidCreateDate()
    {
        $rawData = array(
            \PHPExif\Mapper\Native::DATETIMEORIGINAL => 'Invalid Date String'
        );

        $result = $this->mapper->mapRawData(
            $rawData
        );

        $this->assertCount(0, $result);
        $this->assertNotEquals(
            reset($rawData),
            $result
        );
    }
}
