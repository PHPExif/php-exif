<?php

namespace Tests\Unit\PHPExif\Data;

use Mockery as m;
use PHPExif\Data\Exif;
use PHPExif\Data\Iptc;
use PHPExif\Data\Metadata;

/**
 * @coversDefaultClass \PHPExif\Data\Metadata
 * @covers ::<!public>
 */
class MetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group data
     *
     * @covers ::__construct
     *
     * @return void
     */
    public function testConstructorSetsInProperties()
    {
        $exif = new Exif;
        $iptc = new Iptc;
        $original = new Metadata(
            $exif,
            $iptc
        );

        foreach (['exif', 'iptc'] as $propName) {
            $reflProp = new \ReflectionProperty(Metadata::class, $propName);
            $reflProp->setAccessible(true);

            $this->assertSame(
                $$propName,
                $reflProp->getValue($original)
            );
        }
    }

    /**
     * @group data
     *
     * @covers ::withExif
     *
     * @return void
     */
    public function testWithExifReturnsClone()
    {
        $original = new Metadata(
            new Exif,
            new Iptc
        );

        $newExif = new Exif;

        $other = $original->withExif($newExif);

        $this->assertNotSame(
            $original,
            $other
        );
    }

    /**
     * @group data
     *
     * @covers ::withIptc
     *
     * @return void
     */
    public function testWithIptcReturnsClone()
    {
        $original = new Metadata(
            new Exif,
            new Iptc
        );

        $newIptc = new Iptc;

        $other = $original->withIptc($newIptc);

        $this->assertNotSame(
            $original,
            $other
        );
    }

    /**
     * @group data
     *
     * @covers ::getExif
     *
     * @return void
     */
    public function testGetExifReturnsFromProperty()
    {
        $exif = new Exif;
        $iptc = new Iptc;
        $original = new Metadata(
            $exif,
            $iptc
        );

        $this->assertSame(
            $exif,
            $original->getExif()
        );
    }

    /**
     * @group data
     *
     * @covers ::getIptc
     *
     * @return void
     */
    public function testGetIptcReturnsFromProperty()
    {
        $exif = new Exif;
        $iptc = new Iptc;
        $original = new Metadata(
            $exif,
            $iptc
        );

        $this->assertSame(
            $iptc,
            $original->getIptc()
        );
    }
}
