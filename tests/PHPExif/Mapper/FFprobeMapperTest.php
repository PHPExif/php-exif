<?php

use FFMpeg\FFProbe as FFMpegFFProbe;
use PHPExif\Contracts\MapperInterface;
use PHPExif\Mapper\FFprobe;

class FFprobeMapperTest extends \PHPUnit\Framework\TestCase
{
    protected $mapper;

    public function setUp(): void
    {
        $this->mapper = new FFprobe();
    }

    /**
     * @group mapper
     */
    public function testClassImplementsCorrectInterface()
    {
        $this->assertInstanceOf(MapperInterface::class, $this->mapper);
    }

    /**
     * @group mapper
     */
    public function testMapRawDataIgnoresFieldIfItDoesntExist()
    {
        $rawData = array('foo' => 'bar');
        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertCount(0, $mapped);
    }

    /**
     * @group mapper
     */
    public function testMapRawDataMapsFieldsCorrectly()
    {
        $reflProp = new \ReflectionProperty(get_class($this->mapper), 'map');
        $reflProp->setAccessible(true);
        $map = $reflProp->getValue($this->mapper);

        // ignore custom formatted data stuff:
        unset($map[FFprobe::FILESIZE]);
        unset($map[FFprobe::FILENAME]);
        unset($map[FFprobe::MIMETYPE]);
        unset($map[FFprobe::GPSLATITUDE]);
        unset($map[FFprobe::GPSLONGITUDE]);
        unset($map[FFprobe::QUICKTIME_GPSALTITUDE]);
        unset($map[FFprobe::QUICKTIME_GPSLATITUDE]);
        unset($map[FFprobe::QUICKTIME_GPSLONGITUDE]);
        unset($map[FFprobe::QUICKTIME_DESCRIPTION]);
        unset($map[FFprobe::QUICKTIME_MAKE]);
        unset($map[FFprobe::QUICKTIME_MODEL]);
        unset($map[FFprobe::QUICKTIME_CONTENTIDENTIFIER]);
        unset($map[FFprobe::QUICKTIME_DESCRIPTION]);
        unset($map[FFprobe::QUICKTIME_TITLE]);
        unset($map[FFprobe::QUICKTIME_DATE]);
        unset($map[FFprobe::QUICKTIME_KEYWORDS]);
        unset($map[FFprobe::FRAMERATE]);
        unset($map[FFprobe::DURATION]);

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
     */
    public function testMapRawDataCorrectlyFormatsDateTimeOriginal()
    {
        $rawData = array(
            FFprobe::DATETIMEORIGINAL => '2015:04:01 12:11:09',
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
     */
    public function testMapRawDataCorrectlyFormatsCreationDateQuicktime()
    {
        $rawData = array(
            FFprobe::QUICKTIME_DATE => '2015-04-01T12:11:09+0200',
            FFprobe::DATETIMEORIGINAL => '2015-04-01T12:11:09.000000Z',
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
     */
    public function testMapRawDataCorrectlyFormatsCreationDateWithTimeZone()
    {
        $rawData = array(
            FFprobe::DATETIMEORIGINAL => '2015:04:01 12:11:09+0200',
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
     */
    public function testMapRawDataCorrectlyIgnoresIncorrectDateTimeOriginal()
    {
        $rawData = array(
            FFprobe::DATETIMEORIGINAL => '2015:04:01',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(false, reset($mapped));
    }

    /**
     * @group mapper
     */
    public function testMapRawDataCorrectlyIgnoresIncorrectDateTimeOriginal2()
    {
        $rawData = array(
            FFprobe::QUICKTIME_DATE => '2015:04:01',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(false, reset($mapped));
    }

    /**
     * @group mapper
     */
    public function testMapRawDataCorrectlyFormatsQuickTimeGPSData()
    {
        $expected = array(
          '+27.5916+86.5640+8850/' => array(
                          \PHPExif\Exif::LATITUDE => '27.5916',
                          \PHPExif\Exif::LONGITUDE => '86.5640',
                          \PHPExif\Exif::ALTITUDE => '8850',
                      ),
        );


        foreach ($expected as $key => $value) {
            $result = $this->mapper->mapRawData(array('com.apple.quicktime.location.ISO6709' => $key));

            $this->assertEquals($value[\PHPExif\Exif::LATITUDE], $result[\PHPExif\Exif::LATITUDE]);
            $this->assertEquals($value[\PHPExif\Exif::LONGITUDE], $result[\PHPExif\Exif::LONGITUDE]);
            $this->assertEquals($value[\PHPExif\Exif::ALTITUDE], $result[\PHPExif\Exif::ALTITUDE]);
        }
    }

    /**
     * @group mapper
     */
    public function testMapRawDataCorrectlyRotatesDimensions()
    {
        $expected = array(
          '600' => array(
                          'tags' => array('rotate' => '90'),
                          'width' => '800',
                          'height' => '600',
                      ),
        );


        foreach ($expected as $key => $value) {
            $result = $this->mapper->mapRawData($value);

            $this->assertEquals($key, $result[\PHPExif\Exif::WIDTH]);
        }
    }

    /**
     * @group mapper
     */
    public function testMapRawDataCorrectlyFormatsGPSData()
    {
        $expected = array(
            '+40.333452380952,+20.167314814815' => array(
                'location'     => '+40.333452380952+20.167314814815/',
            ),
            '+0,+0' => array(
                'location'     => '+0+0/',
            ),
            '+71.706936,-42.604303' => array(
                'location'     => '+71.706936-42.604303/',
            ),
        );

        foreach ($expected as $key => $value) {
            $result = $this->mapper->mapRawData($value);
            $this->assertEquals($key, $result[\PHPExif\Exif::GPS]);
        }
    }

    /**
     * @group mapper
     */
    public function testMapRawDataCorrectlyFramerate()
    {
        $expected = array(
            '30' => array(
                'avg_frame_rate'     => '30',
            ),
            '20' => array(
                'avg_frame_rate'     => '200/10',
            )
        );

        foreach ($expected as $key => $value) {
            $result = $this->mapper->mapRawData($value);
            $this->assertEquals($key, reset($result));
        }
    }

    public function testMapRawDataCorrectlyFormatsDifferentDateTimeString()
    {
        $rawData = array(
            FFprobe::DATETIMEORIGINAL => '2014-12-15 00:12:00'
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
            FFprobe::DATETIMEORIGINAL => 'Invalid Date String'
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
     */
    public function testNormalizeComponentCorrectly()
    {
        $reflMethod = new \ReflectionMethod(FFprobe::class, 'normalizeComponent');
        $reflMethod->setAccessible(true);

        $rawData = array(
            '2/800' => 0.0025,
            '1/400' => 0.0025,
            '0/1'   => 0,
            '1/0'   => 0,
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
     */
    public function testMapRawDataMatchesFieldsWithoutCaseSensibilityOnFirstLetter()
    {
        $rawData = array(
            'Width' => '800',
            'mimeType' => 'video/quicktime',
        );
        $mapped = $this->mapper->mapRawData($rawData);
        $this->assertCount(2, $mapped);
        $keys = array_keys($mapped);

        $expected = array(
            FFprobe::WIDTH,
            FFprobe::MIMETYPE
        );
        $this->assertEquals($expected, $keys);
    }

    /**
     * @group mapper
     */
    public function testreadISO6709()
    {
        $reflMethod = new \ReflectionMethod(FFprobe::class, 'readISO6709');
        $reflMethod->setAccessible(true);

        $testcase = array(
          '+27.5916+086.5640+8850/' => array(
                          'latitude' => '27.5916',
                          'longitude' => '86.5640',
                          'altitude' => '8850',
                      ),
          '+1234.7-09854.1/' => array(
                          'latitude' => '12.578333333333',
                          'longitude' => '-98.901666666667',
                          'altitude' => null,
                      ),
          '+352139+1384339+3776/' => array(
                          'latitude' => '35.360833333333',
                          'longitude' => '138.727500000000',
                          'altitude' => '3776',
                      ),
          '+40.75-074.00/' => array(
                          'latitude' => '40.75',
                          'longitude' => '-74',
                          'altitude' => null,
                      ),
          '+123456.7-0985432.1/' => array(
                          'latitude' => '12.582416666667',
                          'longitude' => '-98.908916666667',
                          'altitude' => null,
                      ),
          '-90+000+2800/' => array(
                          'latitude' => '-90',
                          'longitude' => '0',
                          'altitude' => '2800',
                      ),
          '+35.658632+139.745411/' => array(
                          'latitude' => '35.658632',
                          'longitude' => '139.745411',
                          'altitude' => null,
                      ),
          '+48.8577+002.295/' => array(
                          'latitude' => '48.8577',
                          'longitude' => '2.295',
                          'altitude' => null,
                      ),
          '+48.8577+002.295-50/' => array(
                          'latitude' => '48.8577',
                          'longitude' => '2.295',
                          'altitude' => '-50',
                      ),
        );

        foreach ($testcase as $key => $expected) {
            $result = $reflMethod->invoke($this->mapper, $key);
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @group mapper
     */
    public function testconvertDMStoDecimal()
    {

        $reflMethod = new \ReflectionMethod(FFprobe::class, 'convertDMStoDecimal');
        $reflMethod->setAccessible(true);

        $testcase = array(
          '+27.5916' => array(
                          'sign' => '+',
                          'degrees' => '27',
                          'minutes' => '',
                          'seconds' => '',
                          'fraction' => '.5916',
                      ),
          '+86.5640' => array(
                          'sign' => '+',
                          'degrees' => '86',
                          'minutes' => '',
                          'seconds' => '',
                          'fraction' => '.5640',
                      ),
          '12.578333333333' => array(
                          'sign' => '+',
                          'degrees' => '12',
                          'minutes' => '34',
                          'seconds' => '',
                          'fraction' => '.7',
                      ),
          '-98.901666666667' => array(
                          'sign' => '-',
                          'degrees' => '098',
                          'minutes' => '54',
                          'seconds' => '',
                          'fraction' => '.1',
                      ),
          '+35.360833333333' => array(
                          'sign' => '+',
                          'degrees' => '35',
                          'minutes' => '21',
                          'seconds' => '39',
                          'fraction' => '',
                      ),
          '+138.72750000000' => array(
                          'sign' => '+',
                          'degrees' => '138',
                          'minutes' => '43',
                          'seconds' => '39',
                          'fraction' => '',
                      ),
          '12.582416666667' => array(
                          'sign' => '+',
                          'degrees' => '12',
                          'minutes' => '34',
                          'seconds' => '56',
                          'fraction' => '.7',
                      ),
          '-98.908916666667' => array(
                          'sign' => '-',
                          'degrees' => '098',
                          'minutes' => '54',
                          'seconds' => '32',
                          'fraction' => '.1',
                      ),
        );
        foreach ($testcase as $expected => $key) {
            $result = $reflMethod->invoke($this->mapper, $key['sign'], $key['degrees'], $key['minutes'], $key['seconds'], $key['fraction']);
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\FFprobe::mapRawData
     */
    public function testMapRawDataCorrectlyKeywords()
    {
        $rawData = array(
            \PHPExif\Mapper\FFprobe::QUICKTIME_KEYWORDS => 'Keyword_1 Keyword_2',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(
            ['Keyword_1 Keyword_2'],
            reset($mapped)
        );
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\FFprobe::mapRawData
     */
    public function testMapRawDataCorrectlySplitKeywords()
    {
        $rawData = array(
            \PHPExif\Mapper\FFprobe::QUICKTIME_KEYWORDS => 'Keyword_1,Keyword_2',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(
            ['Keyword_1', 'Keyword_2'],
            reset($mapped)
        );
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\FFprobe::mapRawData
     */
    public function testMapRawDataCorrectlyArrayKeywords()
    {
        $rawData = array(
            \PHPExif\Mapper\FFprobe::QUICKTIME_KEYWORDS => array('Keyword_1', 'Keyword_2'),
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(
            ['Keyword_1', 'Keyword_2'],
            reset($mapped)
        );
    }
}
