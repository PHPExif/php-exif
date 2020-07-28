<?php
/**
 * @covers \PHPExif\Mapper\Exiftool::<!public>
 */
class ExiftoolMapperTest extends \PHPUnit\Framework\TestCase
{
    protected $mapper;

    public function setUp(): void
    {
        $this->mapper = new \PHPExif\Mapper\Exiftool;
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
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataIgnoresFieldIfItDoesntExist()
    {
        $rawData = array('foo' => 'bar');
        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertCount(0, $mapped);
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataMapsFieldsCorrectly()
    {
        $reflProp = new \ReflectionProperty(get_class($this->mapper), 'map');
        $reflProp->setAccessible(true);
        $map = $reflProp->getValue($this->mapper);

        // ignore custom formatted data stuff:
        unset($map[\PHPExif\Mapper\Exiftool::APERTURE]);
        unset($map[\PHPExif\Mapper\Exiftool::APPROXIMATEFOCUSDISTANCE]);
        unset($map[\PHPExif\Mapper\Exiftool::DATETIMEORIGINAL]);
        unset($map[\PHPExif\Mapper\Exiftool::EXPOSURETIME]);
        unset($map[\PHPExif\Mapper\Exiftool::FOCALLENGTH]);
        unset($map[\PHPExif\Mapper\Exiftool::GPSLATITUDE]);
        unset($map[\PHPExif\Mapper\Exiftool::GPSLONGITUDE]);
        unset($map[\PHPExif\Mapper\Exiftool::CAPTION]);
        unset($map[\PHPExif\Mapper\Exiftool::CONTENTIDENTIFIER]);
        unset($map[\PHPExif\Mapper\Exiftool::KEYWORDS]);
        unset($map[\PHPExif\Mapper\Exiftool::DATETIMEORIGINAL]);
        unset($map[\PHPExif\Mapper\Exiftool::DATETIMEORIGINAL_QUICKTIME]);
        unset($map[\PHPExif\Mapper\Exiftool::MAKE_QUICKTIME]);
        unset($map[\PHPExif\Mapper\Exiftool::MODEL_QUICKTIME]);
        unset($map[\PHPExif\Mapper\Exiftool::FRAMERATE]);
        unset($map[\PHPExif\Mapper\Exiftool::FRAMERATE_QUICKTIME_1]);
        unset($map[\PHPExif\Mapper\Exiftool::FRAMERATE_QUICKTIME_2]);
        unset($map[\PHPExif\Mapper\Exiftool::FRAMERATE_QUICKTIME_3]);
        unset($map[\PHPExif\Mapper\Exiftool::DURATION]);
        unset($map[\PHPExif\Mapper\Exiftool::DURATION_QUICKTIME]);
        unset($map[\PHPExif\Mapper\Exiftool::GPSLATITUDE_QUICKTIME]);
        unset($map[\PHPExif\Mapper\Exiftool::GPSLONGITUDE_QUICKTIME]);
        unset($map[\PHPExif\Mapper\Exiftool::GPSALTITUDE_QUICKTIME]);
        unset($map[\PHPExif\Mapper\Exiftool::MICROVIDEOOFFSET]);
        unset($map[\PHPExif\Mapper\Exiftool::CITY]);
        unset($map[\PHPExif\Mapper\Exiftool::SUBLOCATION]);
        unset($map[\PHPExif\Mapper\Exiftool::STATE]);
        unset($map[\PHPExif\Mapper\Exiftool::COUNTRY]);
        unset($map[\PHPExif\Mapper\Exiftool::LENS_ID]);

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
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsAperture()
    {
        $rawData = array(
            \PHPExif\Mapper\Exiftool::APERTURE => 0.123,
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals('f/0.1', reset($mapped));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsFocusDistance()
    {
        $rawData = array(
            \PHPExif\Mapper\Exiftool::APPROXIMATEFOCUSDISTANCE => 50,
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals('50m', reset($mapped));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsCreationDate()
    {
        $rawData = array(
            \PHPExif\Mapper\Exiftool::DATETIMEORIGINAL => '2015:04:01 12:11:09',
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
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsCreationDateWithTimeZone()
    {
        $data = array (
          array(
            \PHPExif\Mapper\Exiftool::DATETIMEORIGINAL => '2015:04:01 12:11:09+0200',
          ),
          array(
              \PHPExif\Mapper\Exiftool::DATETIMEORIGINAL => '2015:04:01 12:11:09',
              'ExifIFD:OffsetTimeOriginal' => '+0200',
          ),
          array(
              \PHPExif\Mapper\Exiftool::DATETIMEORIGINAL_QUICKTIME => '2015-04-01T12:11:09+0200',
              \PHPExif\Mapper\Exiftool::DATETIMEORIGINAL => '2015:04:01 12:11:09',
              'ExifIFD:OffsetTimeOriginal' => '+0200',
          )
        );

        foreach ($data as $key => $rawData) {
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

    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsCreationDateWithTimeZone2()
    {
        $rawData = array(
            \PHPExif\Mapper\Exiftool::DATETIMEORIGINAL => '2015:04:01 12:11:09',
            'ExifIFD:OffsetTimeOriginal' => '+0200',
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
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataCorrectlyIgnoresIncorrectCreationDate()
    {
        $rawData = array(
            \PHPExif\Mapper\Exiftool::DATETIMEORIGINAL => '2015:04:01',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(false, reset($mapped));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataCorrectlyIgnoresIncorrectCreationDate2()
    {
        $rawData = array(
            \PHPExif\Mapper\Exiftool::DATETIMEORIGINAL_QUICKTIME => '2015:04:01',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(false, reset($mapped));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
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
                \PHPExif\Mapper\Exiftool::EXPOSURETIME => $value,
            ));

            $this->assertEquals($expected, reset($mapped));
        }
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsFocalLength()
    {
        $rawData = array(
            \PHPExif\Mapper\Exiftool::FOCALLENGTH => '15 m',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(15, reset($mapped));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsGPSData()
    {
        $this->mapper->setNumeric(false);
        $result = $this->mapper->mapRawData(
            array(
                \PHPExif\Mapper\Exiftool::GPSLATITUDE  => '40 deg 20\' 0.42857" N',
                'GPS:GPSLatitudeRef'                   => 'North',
                \PHPExif\Mapper\Exiftool::GPSLONGITUDE => '20 deg 10\' 2.33333" W',
                'GPS:GPSLongitudeRef'                  => 'West',
            )
        );

        $expected_gps = '40.333452380556,-20.167314813889';
        $expected_lat = '40.333452380556';
        $expected_lon = '-20.167314813889';
        $this->assertCount(3, $result);
        $this->assertEquals($expected_gps, $result['gps']);
        $this->assertEquals($expected_lat, $result['latitude']);
        $this->assertEquals($expected_lon, $result['longitude']);
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataIncorrectlyFormatedGPSData()
    {
        $this->mapper->setNumeric(false);
        $result = $this->mapper->mapRawData(
            array(
                \PHPExif\Mapper\Exiftool::GPSLATITUDE  => '40 degrees 20\' 0.42857" N',
                'GPS:GPSLatitudeRef'                   => 'North',
                \PHPExif\Mapper\Exiftool::GPSLONGITUDE => '20 degrees 10\' 2.33333" W',
                'GPS:GPSLongitudeRef'                  => 'West',
            )
        );

        $expected_gps = false;
        $expected_lat = false;
        $expected_lon = false;
        $this->assertCount(3, $result);
        $this->assertEquals($expected_gps, $result['gps']);
        $this->assertEquals($expected_lat, $result['latitude']);
        $this->assertEquals($expected_lon, $result['longitude']);
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsNumericGPSData()
    {
        $result = $this->mapper->mapRawData(
            array(
                \PHPExif\Mapper\Exiftool::GPSLATITUDE  => '40.333452381',
                'GPS:GPSLatitudeRef'                   => 'North',
                \PHPExif\Mapper\Exiftool::GPSLONGITUDE => '20.167314814',
                'GPS:GPSLongitudeRef'                  => 'West',
            )
        );

        $expected_gps = '40.333452381,-20.167314814';
        $expected_lat = '40.333452381';
        $expected_lon = '-20.167314814';
        $this->assertCount(3, $result);
        $this->assertEquals($expected_gps, $result['gps']);
        $this->assertEquals($expected_lat, $result['latitude']);
        $this->assertEquals($expected_lon, $result['longitude']);
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataOnlyLatitude()
    {
        $result = $this->mapper->mapRawData(
            array(
                \PHPExif\Mapper\Exiftool::GPSLATITUDE => '40.333452381',
                'GPS:GPSLatitudeRef'                  => 'North',
            )
        );

        $this->assertCount(1, $result);
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Exiftool::setNumeric
     */
    public function testSetNumericInProperty()
    {
        $reflProperty = new \ReflectionProperty(get_class($this->mapper), 'numeric');
        $reflProperty->setAccessible(true);

        $expected = true;
        $this->mapper->setNumeric($expected);

        $this->assertEquals($expected, $reflProperty->getValue($this->mapper));
    }

    public function testMapRawDataCorrectlyFormatsDifferentDateTimeString()
    {
        $rawData = array(
            \PHPExif\Mapper\Exiftool::DATETIMEORIGINAL => '2014-12-15 00:12:00'
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
            \PHPExif\Mapper\Exiftool::DATETIMEORIGINAL => 'Invalid Date String'
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
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataCorrectlyAltitude()
    {
        $result = $this->mapper->mapRawData(
            array(
                \PHPExif\Mapper\Exiftool::GPSALTITUDE  => '122.053',
                'GPS:GPSAltitudeRef'                   => '0',
            )
        );
	$expected = 122.053;
        $this->assertEquals($expected, reset($result));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataCorrectlyNegativeAltitude()
    {
        $result = $this->mapper->mapRawData(
            array(
                \PHPExif\Mapper\Exiftool::GPSALTITUDE  => '122.053',
                'GPS:GPSAltitudeRef'                   => '1',
            )
        );
        $expected = '-122.053';
        $this->assertEquals($expected, reset($result));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsQuicktimeGPSData()
    {
        $result = $this->mapper->mapRawData(
            array(
                \PHPExif\Mapper\Exiftool::GPSLATITUDE_QUICKTIME  => '40.333',
                'GPS:GPSLatitudeRef'                             => 'North',
                \PHPExif\Mapper\Exiftool::GPSLONGITUDE_QUICKTIME => '-20.167',
                'GPS:GPSLongitudeRef'                            => 'West',
            )
        );
        $expected_gps = '40.333,-20.167';
        $expected_lat = '40.333';
        $expected_lon = '-20.167';
        $this->assertCount(3, $result);
        $this->assertEquals($expected_gps, $result['gps']);
        $this->assertEquals($expected_lat, $result['latitude']);
        $this->assertEquals($expected_lon, $result['longitude']);
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataCorrectlyQuicktimeAltitude()
    {
        $result = $this->mapper->mapRawData(
            array(
                \PHPExif\Mapper\Exiftool::GPSALTITUDE_QUICKTIME  => '122.053',
                'Composite:GPSAltitudeRef'                       => '1',
            )
        );
        $expected = -122.053;
        $this->assertEquals($expected, reset($result));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\Exiftool::mapRawData
     */
    public function testMapRawDataCorrectlyHeightVideo()
    {

      $rawData = array(
          '600'  => array(
                            \PHPExif\Mapper\Exiftool::IMAGEHEIGHT_VIDEO  => '800x600',
                        ),
          '600'  => array(
                            \PHPExif\Mapper\Exiftool::IMAGEHEIGHT_VIDEO  => '800x600',
                            'Composite:Rotation'                        => '0',
                        ),
          '800'  => array(
                            \PHPExif\Mapper\Exiftool::IMAGEHEIGHT_VIDEO  => '800x600',
                            'Composite:Rotation'                        => '90',
                       ),
          '800'  => array(
                            \PHPExif\Mapper\Exiftool::IMAGEHEIGHT_VIDEO  => '800x600',
                            'Composite:Rotation'                        => '270',
                        ),
          '600'  => array(
                            \PHPExif\Mapper\Exiftool::IMAGEHEIGHT_VIDEO  => '800x600',
                            'Composite:Rotation'                        => '360',
                        ),
          '600'  => array(
                            \PHPExif\Mapper\Exiftool::IMAGEHEIGHT_VIDEO  => '800x600',
                            'Composite:Rotation'                        => '180',
                        ),
          '600'  => array(
                            \PHPExif\Mapper\Exiftool::IMAGEHEIGHT_VIDEO  => '800 600',
                            'Composite:Rotation'                        => '180',
                        ),
      );

      foreach ($rawData as $expected => $value) {
          $mapped = $this->mapper->mapRawData($value);

          $this->assertEquals($expected, $mapped['height']);
      }
    }



        /**
         * @group mapper
         * @covers \PHPExif\Mapper\Exiftool::mapRawData
         */
        public function testMapRawDataCorrectlyWidthVideo()
        {

          $rawData = array(
              '800'  => array(
                                \PHPExif\Mapper\Exiftool::IMAGEWIDTH_VIDEO  => '800x600',
                            ),
              '800'  => array(
                                \PHPExif\Mapper\Exiftool::IMAGEWIDTH_VIDEO  => '800x600',
                                'Composite:Rotation'                        => '0',
                            ),
              '600'  => array(
                                \PHPExif\Mapper\Exiftool::IMAGEWIDTH_VIDEO  => '800x600',
                                'Composite:Rotation'                        => '90',
                            ),
              '600'  => array(
                                \PHPExif\Mapper\Exiftool::IMAGEWIDTH_VIDEO  => '800x600',
                                'Composite:Rotation'                        => '270',
                            ),
              '800'  => array(
                                \PHPExif\Mapper\Exiftool::IMAGEWIDTH_VIDEO  => '800x600',
                                'Composite:Rotation'                        => '360',
                            ),
              '800'  => array(
                                \PHPExif\Mapper\Exiftool::IMAGEWIDTH_VIDEO  => '800x600',
                                'Composite:Rotation'                        => '180',
                            ),
              '800'  => array(
                                \PHPExif\Mapper\Exiftool::IMAGEWIDTH_VIDEO  => '800 600',
                                'Composite:Rotation'                        => '180',
                            ),
          );

          foreach ($rawData as $expected => $value) {
              $mapped = $this->mapper->mapRawData($value);

              $this->assertEquals($expected, $mapped['width']);
          }
        }


        /**
         * @group mapper
         * @covers \PHPExif\Mapper\Exiftool::mapRawData
         */
        public function testMapRawDataCorrectlyIsoFormats()
        {
            $expected = array(
                '80' => array(
                    'ExifIFD:ISO'     => '80',
                ),
                '800' => array(
                    'ExifIFD:ISO'     => '800 0 0',
                ),
            );

            foreach ($expected as $key => $value) {
    		        $result = $this->mapper->mapRawData($value);
    	          $this->assertEquals($key, reset($result));
            }
        }
}
