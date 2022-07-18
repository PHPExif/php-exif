<?php
/**
 * @covers \PHPExif\Mapper\Native::<!public>
 */
class NativeMapperTest extends \PHPUnit\Framework\TestCase
{
    protected $mapper;

    public function setUp(): void
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
        unset($map[\PHPExif\Mapper\Native::FRAMERATE]);
        unset($map[\PHPExif\Mapper\Native::DURATION]);
        unset($map[\PHPExif\Mapper\Native::CITY]);
        unset($map[\PHPExif\Mapper\Native::SUBLOCATION]);
        unset($map[\PHPExif\Mapper\Native::STATE]);
        unset($map[\PHPExif\Mapper\Native::COUNTRY]);

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
        public function testMapRawDataCorrectlyFormatsCreationDateWithTimeZone()
        {
            $rawData = array(
                \PHPExif\Mapper\Native::DATETIMEORIGINAL => '2015:04:01 12:11:09+0200',
            );

            $mapped = $this->mapper->mapRawData($rawData);

            $result = reset($mapped);
            $this->assertInstanceOf('\\DateTime', $result);
            $this->assertEquals(
                '2015:04:01 12:11:09',
                $result->format('Y:m:d H:i:s')
            );
            $this->assertEquals(
                7200,
                $result->getOffset()
            );
            $this->assertEquals(
                '+02:00',
                $result->getTimezone()->getName()
            );
        }

        /**
         * @group mapper
         * @covers \PHPExif\Mapper\Native::mapRawData
         */
        public function testMapRawDataCorrectlyFormatsCreationDateWithTimeZone2()
        {
            $rawData = array(
                \PHPExif\Mapper\Native::DATETIMEORIGINAL => '2015:04:01 12:11:09',
                'UndefinedTag:0x9011' => '+0200',
            );

            $mapped = $this->mapper->mapRawData($rawData);

            $result = reset($mapped);
            $this->assertInstanceOf('\\DateTime', $result);
            $this->assertEquals(
                '2015:04:01 12:11:09',
                $result->format('Y:m:d H:i:s')
            );
            $this->assertEquals(
                7200,
                $result->getOffset()
            );
            $this->assertEquals(
                '+02:00',
                $result->getTimezone()->getName()
            );
        }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::mapRawData
     */
    public function testMapRawDataCorrectlyIgnoresIncorrectDateTimeOriginal()
    {
        $rawData = array(
            \PHPExif\Mapper\Native::DATETIMEORIGINAL => '2015:04:01',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(false, reset($mapped));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::mapRawData
     */
    public function testMapRawDataCorrectlyIgnoresIncorrectTimeZone()
    {
        $rawData = array(
            \PHPExif\Mapper\Native::DATETIMEORIGINAL => '2015:04:01 12:11:09',
            'UndefinedTag:0x9011' => '   :  ',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $result = reset($mapped);
        $this->assertInstanceOf('\\DateTime', $result);
        $this->assertEquals(
            '2015:04:01 12:11:09',
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
            '1/30'  => 10/300,
            '1/400' => 2/800,
            '1/400' => 1/400,
            '0'     => 0,
        );

        foreach ($rawData as $expected => $value) {
            $mapped = $this->mapper->mapRawData(array(
                \PHPExif\Mapper\Native::EXPOSURETIME => $value,
            ));

            $this->assertEquals($expected, reset($mapped));
        }
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
    public function testMapRawDataCorrectlyFormatsFocalLengthDivisionByZero()
    {
        $rawData = array(
            \PHPExif\Mapper\Native::FOCALLENGTH => '1/0',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(0, reset($mapped));
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
    public function testMapRawDataMatchesFieldsWithoutCaseSensibilityOnFirstLetter()
    {
        $rawData = array(
            \PHPExif\Mapper\Native::ORIENTATION => 'Portrait',
            'Copyright' => 'Acme',
        );
        $mapped = $this->mapper->mapRawData($rawData);
        $this->assertCount(2, $mapped);
        $keys = array_keys($mapped);

        $expected = array(
            \PHPExif\Mapper\Native::ORIENTATION,
            \PHPExif\Mapper\Native::COPYRIGHT
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
                'GPSLatitude'     => array('0/1', '0/1', '0/1'),
                'GPSLatitudeRef'  => 'N',
                'GPSLongitude'    => array('0/1', '0/1', '0/1'),
                'GPSLongitudeRef' => 'W',
            ),
            '71.706936,-42.604303' => array(
                'GPSLatitude'     => array('71.706936'),
                'GPSLatitudeRef'  => 'N',
                'GPSLongitude'    => array('42.604303'),
                'GPSLongitudeRef' => 'W',
            ),
        );

        foreach ($expected as $key => $value) {
            $result = $this->mapper->mapRawData($value);
            $this->assertEquals($key, $result[\PHPExif\Exif::GPS]);
        }
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::mapRawData
     */
    public function testMapRawDataCorrectlyIgnoresEmptyGPSData()
    {
        $result = $this->mapper->mapRawData(
            array(
                'GPSLatitude'     => array('0/0', '0/0', '0/0'),
                'GPSLatitudeRef'  => null,
                'GPSLongitude'    => array('0/0', '0/0', '0/0'),
                'GPSLongitudeRef' => null,
            )
        );

        $this->assertEquals(false, reset($result));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsAltitudeData()
    {
        $expected = array(
            8848.0 => array(
                'GPSAltitude'     => '8848',
                'GPSAltitudeRef'  => '0',
            ),
            -10994.0 => array(
                'GPSAltitude'     => '10994',
                'GPSAltitudeRef'  => '1',
            ),
        );

        foreach ($expected as $key => $value) {
            $result = $this->mapper->mapRawData($value);
            $this->assertEquals($key, $result[\PHPExif\Exif::ALTITUDE]);
        }
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::mapRawData
     */
    public function testMapRawDataCorrectlyIgnoresIncorrectAltitude()
    {
        $result = $this->mapper->mapRawData(
            array(
                'GPSAltitude'     => "0/0",
                'GPSAltitudeRef'  => chr(0),
            )
        );
        $this->assertEquals(false, reset($result));
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

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::normalizeComponent
     */
    public function testNormalizeComponentCorrectly()
    {
        $reflMethod = new \ReflectionMethod('\PHPExif\Mapper\Native', 'normalizeComponent');
        $reflMethod->setAccessible(true);

        $rawData = array(
            '2/800' => 0.0025,
            '1/400' => 0.0025,
            '0/1'   => 0,
            '0'     => 0,
            'A'     => 0,
            'A/1'     => 0,
            '1/A'     => 0,
            'A/A'     => 0,
        );

        foreach ($rawData as $value => $expected) {
            $normalized = $reflMethod->invoke($this->mapper, $value);

            $this->assertEquals($expected, $normalized);
        }
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::mapRawData
     */
    public function testMapRawDataCorrectlyIsoFormats()
    {
        $expected = array(
            '80' => array(
                'ISOSpeedRatings'     => '80',
            ),
            '800' => array(
                'ISOSpeedRatings'     => '800 0 0',
            ),
            '100' => array(
                'ISOSpeedRatings'     => array('100 0 0', ''),
            ),
        );

        foreach ($expected as $key => $value) {
            $result = $this->mapper->mapRawData($value);
            $this->assertEquals($key, reset($result));
        }
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::mapRawData
     */
    public function testMapRawDataCorrectlyLensData()
    {
        $data = array (
            array(
                \PHPExif\Mapper\Native::LENS => 'LEICA DG 12-60/F2.8-4.0',
            ),
            array(
                \PHPExif\Mapper\Native::LENS => 'LEICA DG 12-60/F2.8-4.0',
                \PHPExif\Mapper\Native::LENS_LR => 'LUMIX G VARIO 12-32/F3.5-5.6',
                \PHPExif\Mapper\Native::LENS_TYPE => 'LUMIX G VARIO 12-32/F3.5-5.6',
            ),
            array(
                \PHPExif\Mapper\Native::LENS_LR => 'LUMIX G VARIO 12-32/F3.5-5.6',
                \PHPExif\Mapper\Native::LENS => 'LEICA DG 12-60/F2.8-4.0',
            ),
            array(
                \PHPExif\Mapper\Native::LENS_TYPE => 'LUMIX G VARIO 12-32/F3.5-5.6',
                \PHPExif\Mapper\Native::LENS => 'LEICA DG 12-60/F2.8-4.0',
            )
        );

        foreach ($data as $key => $rawData) {
            $mapped = $this->mapper->mapRawData($rawData);

            $this->assertEquals(
                'LEICA DG 12-60/F2.8-4.0',
                reset($mapped)
            );
        }
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Native::mapRawData
     */
    public function testMapRawDataCorrectlyLensData2()
    {
        $data = array (
            array(
                \PHPExif\Mapper\Native::LENS_LR => 'LUMIX G VARIO 12-32/F3.5-5.6',
            ),
            array(
                \PHPExif\Mapper\Native::LENS_TYPE => 'LUMIX G VARIO 12-32/F3.5-5.6',
            )
        );

        foreach ($data as $key => $rawData) {
            $mapped = $this->mapper->mapRawData($rawData);

            $this->assertEquals(
                'LUMIX G VARIO 12-32/F3.5-5.6',
                reset($mapped)
            );
        }
    }
}
