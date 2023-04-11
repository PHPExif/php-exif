<?php

use PHPExif\Contracts\MapperInterface;
use PHPExif\Mapper\ImageMagick;

class ImageMagickMapperTest extends \PHPUnit\Framework\TestCase
{
    protected $mapper;

    public function setUp(): void
    {
        $this->mapper = new ImageMagick();
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
        unset($map[ImageMagick::APERTURE]);
        unset($map[ImageMagick::EXPOSURETIME]);
        unset($map[ImageMagick::FOCALLENGTH]);
        unset($map[ImageMagick::GPSLATITUDE]);
        unset($map[ImageMagick::GPSLONGITUDE]);
        unset($map[ImageMagick::DATETIMEORIGINAL]);
        unset($map[ImageMagick::ISO]);
        unset($map[ImageMagick::LENS]);
        unset($map[ImageMagick::WIDTH]);
        unset($map[ImageMagick::HEIGHT]);
        unset($map[ImageMagick::IMAGEHEIGHT_PNG]);
        unset($map[ImageMagick::IMAGEWIDTH_PNG]);
        unset($map[ImageMagick::COPYRIGHT_IPTC]);

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
     */
    public function testMapRawDataCorrectlyFormatsAperture()
    {
        $rawData = array(
            ImageMagick::APERTURE => '54823/32325',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals('f/1.7', reset($mapped));
    }

    /**
     * @group mapper
     */
    public function testMapRawDataCorrectlyFormatsDateTimeOriginal()
    {
        $rawData = array(
            ImageMagick::DATETIMEORIGINAL => '2015:04:01 12:11:09',
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
    public function testMapRawDataCorrectlyFormatsCreationDateWithTimeZone()
    {
        $data = array(
          array(
            ImageMagick::DATETIMEORIGINAL => '2015:04:01 12:11:09+0200',
          ),
          array(
              ImageMagick::DATETIMEORIGINAL => '2015:04:01 12:11:09',
              'exif:OffsetTimeOriginal' => '+0200',
          ),
          array(
              ImageMagick::DATETIMEORIGINAL => '2015:04:01 12:11:09',
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
     */
    public function testMapRawDataCorrectlyFormatsCreationDateWithTimeZone2()
    {
        $rawData = array(
            ImageMagick::DATETIMEORIGINAL => '2015:04:01 12:11:09',
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
     */
    public function testMapRawDataCorrectlyIgnoresIncorrectDateTimeOriginal()
    {
        $rawData = array(
            ImageMagick::DATETIMEORIGINAL => '2015:04:01',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(false, reset($mapped));
    }

    /**
     * @group mapper
     */
    public function testMapRawDataCorrectlyIgnoresIncorrectTimeZone()
    {
        $rawData = array(
            ImageMagick::DATETIMEORIGINAL => '2015:04:01 12:11:09',
            'exif:OffsetTimeOriginal' => '   :  ',
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
                ImageMagick::EXPOSURETIME => $value,
            ));

            $this->assertEquals($expected, reset($mapped));
        }
    }

    /**
     * @group mapper
     */
    public function testMapRawDataCorrectlyFormatsFocalLength()
    {
        $rawData = array(
            ImageMagick::FOCALLENGTH => '15 m',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(15, reset($mapped));
    }

    /**
     * @group mapper
     */
    public function testMapRawDataCorrectlyFormatsGPSData()
    {
        $result = $this->mapper->mapRawData(
            array(
                ImageMagick::GPSLATITUDE  => '40/1, 20/1, 42857/100000',
                'exif:GPSLatitudeRef'                   => 'N',
                ImageMagick::GPSLONGITUDE => '20/1, 10/1, 233333/100000',
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
     */
    public function testMapRawDataIncorrectlyFormatedGPSData()
    {
        $result = $this->mapper->mapRawData(
            array(
                ImageMagick::GPSLATITUDE  => '40/1 20/1 42857/100000',
                'exif:GPSLatitudeRef'                     => 'N',
                ImageMagick::GPSLONGITUDE => '20/1 10/1 233333/100000',
                'exif:GPSLongitudeRef'                    => 'W',
            )
        );
        $this->assertCount(0, $result);
    }

    /**
     * @group mapper
     */
    public function testMapRawDataCorrectlyFormatsNumericGPSData()
    {
        $result = $this->mapper->mapRawData(
            array(
                ImageMagick::GPSLATITUDE  => '40.333452381',
                'exif:GPSLatitudeRef'                   => 'North',
                ImageMagick::GPSLONGITUDE => '20.167314814',
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
     */
    public function testMapRawDataOnlyLatitude()
    {
        $result = $this->mapper->mapRawData(
            array(
                ImageMagick::GPSLATITUDE => '40.333452381',
                'exif:GPSLatitudeRef'                    => 'North',
            )
        );

        $this->assertCount(1, $result);
    }

    /**
     * @group mapper
     */
    public function testMapRawDataCorrectlyIgnoresEmptyGPSData()
    {
        $result = $this->mapper->mapRawData(
            array(
                ImageMagick::GPSLATITUDE  => '0/0, 0/0, 0/0',
                'exif:GPSLatitudeRef'                     => '',
                ImageMagick::GPSLONGITUDE => '0/0, 0/0, 0/0',
                'exif:GPSLongitudeRef'                    => '',
            )
        );

        $this->assertEquals(false, reset($result));
    }


    public function testMapRawDataCorrectlyFormatsDifferentDateTimeString()
    {
        $rawData = array(
            ImageMagick::DATETIMEORIGINAL => '2014-12-15 00:12:00'
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
            ImageMagick::DATETIMEORIGINAL => 'Invalid Date String'
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
    public function testMapRawDataCorrectlyAltitude()
    {
        $result = $this->mapper->mapRawData(
            array(
                ImageMagick::GPSALTITUDE  => '122053/1000',
                'exif:GPSAltitudeRef'                   => '0',
            )
        );
        $expected = 122.053;
        $this->assertEquals($expected, reset($result));
    }

    /**
     * @group mapper
     */
    public function testMapRawDataCorrectlyNegativeAltitude()
    {
        $result = $this->mapper->mapRawData(
            array(
                ImageMagick::GPSALTITUDE  => '122053/1000',
                'exif:GPSAltitudeRef'                   => '1',
            )
        );
        $expected = '-122.053';
        $this->assertEquals($expected, reset($result));
    }

    /**
     * @group mapper
     */
    public function testMapRawDataCorrectlyIgnoresIncorrectAltitude()
    {
        $result = $this->mapper->mapRawData(
            array(
                ImageMagick::GPSALTITUDE  => '0/0',
                'exif:GPSAltitudeRef'                     => '0',
            )
        );
        $this->assertEquals(false, reset($result));
    }


        /**
         * @group mapper
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
                '100' => array(
                    'exif:PhotographicSensitivity'     => '100, 0, 0',
                ),
            );

            foreach ($expected as $key => $value) {
                $result = $this->mapper->mapRawData($value);
                $this->assertEquals($key, reset($result));
            }
        }

        /**
         * @group mapper
             */
        public function testMapRawDataCorrectlyHeightPNG()
        {

            $rawData = array(
                '600'  => array(
                                  ImageMagick::IMAGEHEIGHT_PNG  => '800, 600',
                              ),
            );

            foreach ($rawData as $expected => $value) {
                $mapped = $this->mapper->mapRawData($value);

                $this->assertEquals($expected, $mapped['height']);
            }
        }



      /**
       * @group mapper
         */
      public function testMapRawDataCorrectlyWidthPNG()
      {

          $rawData = array(
              '800'  => array(
                                ImageMagick::IMAGEWIDTH_PNG  => '800, 600',
                            ),
          );

          foreach ($rawData as $expected => $value) {
              $mapped = $this->mapper->mapRawData($value);

              $this->assertEquals($expected, $mapped['width']);
          }
      }

      /**
       * @group mapper
       */
      public function testNormalizeComponentCorrectly()
      {
          $reflMethod = new \ReflectionMethod(ImageMagick::class, 'normalizeComponent');
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
    public function testMapRawDataCorrectlyKeywords()
    {
        $rawData = array(
            ImageMagick::KEYWORDS => 'Keyword_1 Keyword_2',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(
            ['Keyword_1 Keyword_2'],
            reset($mapped)
        );
    }

    /**
     * @group mapper
     */
    public function testMapRawDataCorrectlyKeywordsAndSubject()
    {
        $rawData = array(
            ImageMagick::KEYWORDS => array('Keyword_1', 'Keyword_2'),
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(
            array('Keyword_1' ,'Keyword_2'),
            reset($mapped)
        );
    }

    /**
     * @group mapper
     */
    public function testMapRawDataCorrectlyFormatsXResolution()
    {
        $rawData = array(
            ImageMagick::XRESOLUTION => '1500/300',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(1500, reset($mapped));
    }

    /**
     * @group mapper
     */
    public function testMapRawDataCorrectlyFormatsYResolution()
    {
        $rawData = array(
            ImageMagick::YRESOLUTION => '1500/300',
        );

        $mapped = $this->mapper->mapRawData($rawData);

        $this->assertEquals(1500, reset($mapped));
    }
}
