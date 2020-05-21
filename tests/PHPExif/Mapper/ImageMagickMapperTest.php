<?php
/**
 * @covers \PHPExif\Mapper\ImageMagick::<!public>
 */
class ImageMagickMapperTest extends \PHPUnit\Framework\TestCase
{
    protected $mapper;

    public function setUp(): void
    {
        $this->mapper = new \PHPExif\Mapper\ImageMagick;
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
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataIgnoresFieldIfItDoesntExist()
    {
        $rawData = array('foo' => 'bar');
        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertCount(0, $mapped);
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataMapsFieldsCorrectly()
    {
        $reflProp = new \ReflectionProperty(get_class($this->mapper), 'map');
        $reflProp->setAccessible(true);
        $map = $reflProp->getValue($this->mapper);

        // ignore custom formatted data stuff:
        unset($map[\PHPExif\Mapper\ImageMagick::APERTURE]);
        unset($map[\PHPExif\Mapper\ImageMagick::EXPOSURETIME]);
        unset($map[\PHPExif\Mapper\ImageMagick::FOCALLENGTH]);
        unset($map[\PHPExif\Mapper\ImageMagick::GPSLATITUDE]);
        unset($map[\PHPExif\Mapper\ImageMagick::GPSLONGITUDE]);
        unset($map[\PHPExif\Mapper\ImageMagick::DATETIMEORIGINAL]);
        unset($map[\PHPExif\Mapper\ImageMagick::ISO]);
        unset($map[\PHPExif\Mapper\ImageMagick::LENS]);
        unset($map[\PHPExif\Mapper\ImageMagick::IMAGEWIDTH]);
        unset($map[\PHPExif\Mapper\ImageMagick::IMAGEHEIGHT_PNG]);
        unset($map[\PHPExif\Mapper\ImageMagick::IMAGEWIDTH_PNG]);
        unset($map[\PHPExif\Mapper\ImageMagick::CREATION_DATE]);

        // create raw data
        $keys = array_unique(array_keys($map));
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
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsAperture()
    {
        $rawData = array(
            \PHPExif\Mapper\ImageMagick::APERTURE => '54823/32325',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals('f/1.7', reset($mapped));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsCreationDate()
    {
        $rawData = array(
            \PHPExif\Mapper\ImageMagick::CREATION_DATE => '2015:04:01 12:11:09',
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
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsDateTimeOriginal()
    {
        $rawData = array(
            \PHPExif\Mapper\ImageMagick::DATETIMEORIGINAL => '2015:04:01 12:11:09',
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
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsCreationDateAndDateTimeOriginal1()
    {
        $rawData = array(
            \PHPExif\Mapper\ImageMagick::CREATION_DATE => '2016:04:01 12:11:09',
            \PHPExif\Mapper\ImageMagick::DATETIMEORIGINAL => '2015:04:01 12:11:09',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $result = reset($mapped);
        $expected = new \DateTime('2015:04:01 12:11:09');
        $this->assertInstanceOf('\\DateTime', $result);
        $this->assertEquals(
            $expected,
            $result
        );
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsCreationDateAndDateTimeOriginal2()
    {
        $rawData = array(
            \PHPExif\Mapper\ImageMagick::DATETIMEORIGINAL => '2015:04:01 12:11:09',
            \PHPExif\Mapper\ImageMagick::CREATION_DATE => '2016:04:01 12:11:09',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $result = reset($mapped);
        $expected = new \DateTime('2015:04:01 12:11:09');
        $this->assertInstanceOf('\\DateTime', $result);
        $this->assertEquals(
            $expected,
            $result
        );
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsCreationDateWithTimeZone()
    {
        $data = array (
          array(
            \PHPExif\Mapper\ImageMagick::DATETIMEORIGINAL => '2015:04:01 12:11:09+0200',
          ),
          array(
              \PHPExif\Mapper\ImageMagick::DATETIMEORIGINAL => '2015:04:01 12:11:09',
              'exif:OffsetTimeOriginal' => '+0200',
          ),
          array(
              \PHPExif\Mapper\ImageMagick::DATETIMEORIGINAL => '2015:04:01 12:11:09',
              'exif:OffsetTimeOriginal' => '+0200',
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
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsCreationDateWithTimeZone2()
    {
        $rawData = array(
            \PHPExif\Mapper\ImageMagick::DATETIMEORIGINAL => '2015:04:01 12:11:09',
            'exif:OffsetTimeOriginal' => '+0200',
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
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataCorrectlyIgnoresIncorrectCreationDate()
    {
        $rawData = array(
            \PHPExif\Mapper\ImageMagick::CREATION_DATE => '2015:04:01',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(false, reset($mapped));
    }


    /**
     * @group mapper
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataCorrectlyIgnoresIncorrectDateTimeOriginal()
    {
        $rawData = array(
            \PHPExif\Mapper\ImageMagick::DATETIMEORIGINAL => '2015:04:01',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(false, reset($mapped));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
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
                \PHPExif\Mapper\ImageMagick::EXPOSURETIME => $value,
            ));

            $this->assertEquals($expected, reset($mapped));
        }
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsFocalLength()
    {
        $rawData = array(
            \PHPExif\Mapper\ImageMagick::FOCALLENGTH => '15 m',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(15, reset($mapped));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsGPSData()
    {
        $result = $this->mapper->mapRawData(
            array(
                \PHPExif\Mapper\ImageMagick::GPSLATITUDE  => '40/1, 20/1, 42857/100000',
                'exif:GPSLatitudeRef'                   => 'N',
                \PHPExif\Mapper\ImageMagick::GPSLONGITUDE => '20/1, 10/1, 233333/100000',
                'exif:GPSLongitudeRef'                  => 'W',
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
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataIncorrectlyFormatedGPSData()
    {
        $result = $this->mapper->mapRawData(
            array(
                \PHPExif\Mapper\ImageMagick::GPSLATITUDE  => '40/1 20/1 42857/100000',
                'GPS:GPSLatitudeRef'                   => 'N',
                \PHPExif\Mapper\ImageMagick::GPSLONGITUDE => '20/1 10/1 233333/100000',
                'GPS:GPSLongitudeRef'                  => 'W',
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
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataCorrectlyFormatsNumericGPSData()
    {
        $result = $this->mapper->mapRawData(
            array(
                \PHPExif\Mapper\ImageMagick::GPSLATITUDE  => '40.333452381',
                'exif:GPSLatitudeRef'                   => 'North',
                \PHPExif\Mapper\ImageMagick::GPSLONGITUDE => '20.167314814',
                'exif:GPSLongitudeRef'                  => 'West',
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
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataOnlyLatitude()
    {
        $result = $this->mapper->mapRawData(
            array(
                \PHPExif\Mapper\ImageMagick::GPSLATITUDE => '40.333452381',
                'GPS:GPSLatitudeRef'                  => 'North',
            )
        );

        $this->assertCount(1, $result);
    }


    public function testMapRawDataCorrectlyFormatsDifferentDateTimeString()
    {
        $rawData = array(
            \PHPExif\Mapper\ImageMagick::DATETIMEORIGINAL => '2014-12-15 00:12:00'
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
            \PHPExif\Mapper\ImageMagick::DATETIMEORIGINAL => 'Invalid Date String'
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
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataCorrectlyAltitude()
    {
        $result = $this->mapper->mapRawData(
            array(
                \PHPExif\Mapper\ImageMagick::GPSALTITUDE  => '122053/1000',
                'exif:GPSAltitudeRef'                   => '0',
            )
        );
	$expected = 122.053;
        $this->assertEquals($expected, reset($result));
    }

    /**
     * @group mapper
     * @covers \PHPExif\Mapper\ImageMagick::mapRawData
     */
    public function testMapRawDataCorrectlyNegativeAltitude()
    {
        $result = $this->mapper->mapRawData(
            array(
                \PHPExif\Mapper\ImageMagick::GPSALTITUDE  => '122053/1000',
                'exif:GPSAltitudeRef'                   => '1',
            )
        );
        $expected = '-122.053';
        $this->assertEquals($expected, reset($result));
    }


        /**
         * @group mapper
         * @covers \PHPExif\Mapper\ImageMagick::mapRawData
         */
        public function testMapRawDataCorrectlyIsoFormats()
        {
            $expected = array(
                '80' => array(
                    'exif:PhotographicSensitivity'     => '80',
                ),
                '800' => array(
                    'exif:PhotographicSensitivity'     => '800 0 0',
                ),
            );

            foreach ($expected as $key => $value) {
    		        $result = $this->mapper->mapRawData($value);
    	          $this->assertEquals($key, reset($result));
            }
        }

        /**
         * @group mapper
         * @covers \PHPExif\Mapper\ImageMagick::mapRawData
         */
        public function testMapRawDataCorrectlyHeightPNG()
        {

          $rawData = array(
              '600'  => array(
                                \PHPExif\Mapper\ImageMagick::IMAGEHEIGHT_PNG  => '800, 600',
                            ),
          );

          foreach ($rawData as $expected => $value) {
              $mapped = $this->mapper->mapRawData($value);

              $this->assertEquals($expected, $mapped['height']);
          }
        }



      /**
       * @group mapper
       * @covers \PHPExif\Mapper\ImageMagick::mapRawData
       */
      public function testMapRawDataCorrectlyWidthPNG()
      {

        $rawData = array(
            '800'  => array(
                              \PHPExif\Mapper\ImageMagick::IMAGEWIDTH_PNG  => '800, 600',
                          ),
        );

        foreach ($rawData as $expected => $value) {
            $mapped = $this->mapper->mapRawData($value);

            $this->assertEquals($expected, $mapped['width']);
        }
      }

      /**
       * @group mapper
       * @covers \PHPExif\Mapper\ImageMagick::normalizeComponent
       */
      public function testNormalizeComponentCorrectly()
      {
          $reflMethod = new \ReflectionMethod('\PHPExif\Mapper\ImageMagick', 'normalizeComponent');
          $reflMethod->setAccessible(true);

          $rawData = array(
              '2/800' => 0.0025,
              '1/400' => 0.0025,
              '0/1'   => 0,
              '1/0'   => 0,
              '0'     => 0,
          );

          foreach ($rawData as $value => $expected) {
              $normalized = $reflMethod->invoke($this->mapper, $value);

              $this->assertEquals($expected, $normalized);
          }
      }


}
