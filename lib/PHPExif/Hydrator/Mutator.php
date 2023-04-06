<?php

namespace PHPExif\Hydrator;

use PHPExif\Contracts\HydratorInterface;

/**
 * PHP Exif Mutator Hydrator
 *
 * Hydrates an object by setting data with
 * the class mutator methods
 *
 * @category    PHPExif
 * @package     Hydrator
 */
class Mutator implements HydratorInterface
{
    /**
     * Hydrates given array of data into the given Exif object
     *
     * @param object $object
     * @param array $data
     * @return void
     */
    public function hydrate($object, array $data): void
    {
        foreach ($data as $property => $value) {
            if ($value !== null && $value !== '') {
                $mutator = $this->determineMutator($property);

                if (method_exists($object, $mutator)) {
                    $object->$mutator($value); // @phpstan-ignore-line, PhpStan does not like variadic calls
                }
            }
        }
    }

    /**
     * Determines the name of the mutator method for given property name
     *
     * @param string $property  The property to determine the mutator for
     * @return string   The name of the mutator method
     */
    protected function determineMutator(string $property): string
    {
        $method = 'set' . ucfirst($property);
        return $method;
    }
}
