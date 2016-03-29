<?php

namespace Tests\Unit\PHPExif\Data\Exif;

use Mockery as m;
use PHPExif\Data\Exif\Aperture;

/**
 * @coversDefaultClass \PHPExif\Data\Exif\Aperture
 * @covers ::<!public>
 */
class ApertureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group data
     * @group exif
     *
     * @covers ::fromFocalLength
     *
     * @dataProvider fromFocalLengthProvider
     *
     * @param mixed $focalLength
     * @param string $exceptionClass
     * @param string $message
     *
     * @return void
     */
    public function testFromFocalLengthThrowsExceptionsForInvalidArgument(
        $focalLength,
        $exceptionClass,
        $message
    ) {
        try {
            Aperture::fromFocalLength($focalLength);
            $this->fail('Test should not pass');
        } catch (\Exception $e) {
            $this->assertEquals(
                $exceptionClass,
                get_class($e)
            );

            $this->assertEquals(
                $message,
                $e->getMessage()
            );
        }
    }

    /**
     * fromFocalLengthProvider
     *
     * @return array
     */
    public function fromFocalLengthProvider()
    {
        return [
            [
                8.0,
                \InvalidArgumentException::class,
                'focalLength must be a string',
            ],
            [
                'f/8',
                \RuntimeException::class,
                'Given focalLength is not in a valid format. Need: "f/<float>"',
            ],
        ];
    }

    /**
     * @group data
     * @group exif
     *
     * @return void
     */
    public function testFromFocalLengthReturnsInstance()
    {
        $actual = Aperture::fromFocalLength('f/8.0');
        $this->assertInstanceOf(
            Aperture::class,
            $actual
        );
    }

    /**
     * @group data
     * @group exif
     *
     * @covers ::__construct
     *
     * @dataProvider constructorArgumentsProvider
     *
     * @return void
     */
    public function testConstructorValidatesFloat($value)
    {
        try {
            new Aperture($value);
            $this->fail();
        } catch (\InvalidArgumentException $e) {
        }
    }

    /**
     * constructorArgumentsProvider
     *
     * @return array
     */
    public function constructorArgumentsProvider()
    {
        return [
            ['foo'],
            [false],
        ];
    }

    /**
     * @group data
     * @group exif
     *
     * @covers ::jsonSerialize
     *
     * @return void
     */
    public function testJsonSerializeReturnsString()
    {
        $instance = new Aperture(8.0);
        $actual = $instance->jsonSerialize();

        $this->assertInternalType(
            'string',
            $actual
        );

        $this->assertEquals(
            '"f\/8.0"',
            json_encode($instance)
        );
    }

    /**
     * @group data
     * @group exif
     *
     * @covers ::__toString
     *
     * @return void
     */
    public function testToStringReturnsString()
    {
        $instance = new Aperture(8.0);
        $actual = (string) $instance;

        $this->assertInternalType(
            'string',
            $actual
        );

        $this->assertEquals(
            'f/8.0',
            $actual
        );
    }
}
