<?php
/**
 * Aperture Value Object
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 */

namespace PHPExif\Data\Exif;

/*i
 * Aperture class
 *
 * A value object to describe the Aperture f-number
 *
 * @category    PHPExif
 * @package     Exif
 */
final class Aperture implements \JsonSerializable
{
    /**
     * The f-number
     *
     * @see https://en.wikipedia.org/wiki/F-number
     *
     * @var float
     */
    private $fNumber;

    /**
     * @param float $fNumber
     *
     * @throws \InvalidArgumentException If given f-number is not a float
     */
    public function __construct($fNumber)
    {
        if (filter_var($fNumber, FILTER_VALIDATE_FLOAT) === false) {
            throw new \InvalidArgumentException('fNumber must be a float');
        }

        $this->fNumber = $fNumber;
    }

    /**
     * Creates new instance from given Focal Length format
     *
     * @param string $focalLength
     *
     * @throws \InvalidArgumentException If focalLength is not a string
     */
    public static function fromFocalLength($focalLength)
    {
        if (!is_string($focalLength)) {
            throw new \InvalidArgumentException('focalLength must be a string');
        }

        if (!preg_match('#f/([0-9]*\.[0-9]?)#', $focalLength, $matches)) {
            throw new \RuntimeException('Given focalLength is not in a valid format. Need: "f/<float>"');
        }

        $fNumber = (float) $matches[1];

        return new static($fNumber);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return (string) $this;
    }

    /**
     * Returns string representation
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            'f/%1.1f',
            $this->fNumber
        );
    }
}
