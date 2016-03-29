<?php

namespace Tests\Unit\PHPExif\Data;

use Mockery as m;
use PHPExif\Data\Exif;
use PHPExif\Data\ExifInterface;
use PHPExif\Data\Exif\Aperture;

/**
 * @coversDefaultClass \PHPExif\Data\Exif
 * @covers ::<!public>
 */
class ExifTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group data
     *
     * @covers ::toArray
     *
     * @return void
     */
    public function testToArrayReturnsListOfData()
    {
        $properties = [
            ExifInterface::APERTURE => null,
        ];

        $exif = new Exif;

        $this->assertEquals(
            $properties,
            $exif->toArray()
        );
    }

    /**
     * @group data
     *
     * @covers ::toArray
     *
     * @return void
     */
    public function testToArrayFiltersEmpty()
    {
        $properties = [];

        $exif = new Exif;

        $this->assertEquals(
            $properties,
            $exif->toArray(false)
        );
    }

    /**
     * @group data
     *
     * @covers ::withAperture
     *
     * @return void
     */
    public function testWithApertureReturnsClone()
    {
        $value = Aperture::fromFocalLength('f/8.0');

        $exif = new Exif;
        $other = $exif->withAperture($value);

        $this->assertInstanceOf(
            Exif::class,
            $other
        );
        $this->assertNotSame(
            $exif,
            $other
        );
        $this->assertEmpty($exif->getAperture());
    }

    /**
     * @group data
     *
     * @covers ::getAperture
     *
     * @return void
     */
    public function testGetApertureReturnsFromProperty()
    {
        $exif = new Exif;
        $value = 'foo';

        $reflProp = new \ReflectionProperty(
            Exif::class,
            'aperture'
        );
        $reflProp->setAccessible(true);

        $this->assertEmpty($exif->getAperture());

        $reflProp->setValue(
            $exif,
            $value
        );

        $this->assertEquals(
            $value,
            $exif->getAperture()
        );
    }
}
