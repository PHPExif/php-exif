<?php
/**
 * PHP Exif Reader Adapter Abstract: Common functionality for adapters
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Reader
 */

namespace PHPExif\Reader;

/**
 * PHP Exif Reader Adapter Abstract
 *
 * Implements common functionality for the reader adapters
 *
 * @category    PHPExif
 * @package     Reader
 */
abstract class AdapterAbstract implements AdapterInterface
{
    /**
     * Class constructor
     *
     * @param array $data Optional array of data to initialize the object with
     */
    public function __construct(array $options = array())
    {
        if (!empty($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Set array of options in the current object
     *
     * @param array $options
     * @return \PHPExif\Reader\AdapterAbstract
     */
    public function setOptions(array $options)
    {
        foreach ($options as $property => $value) {
            $setter = $this->determinePropertySetter($property);
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }

        return $this;
    }

    /**
     * Detemines the name of the getter method for given property name
     *
     * @param string $property  The property to determine the getter for
     * @return string   The name of the getter method
     */
    protected function determinePropertyGetter($property)
    {
        $method = 'get' . ucfirst($property);
        return $method;
    }

    /**
     * Detemines the name of the setter method for given property name
     *
     * @param string $property  The property to determine the setter for
     * @return string   The name of the setter method
     */
    protected function determinePropertySetter($property)
    {
        $method = 'set' . ucfirst($property);
        return $method;
    }

    /**
     * Get a list of the class constants prefixed with given $type
     *
     * @param string $type
     * @return array
     */
    public function getClassConstantsOfType($type)
    {
        $class = new \ReflectionClass(get_called_class());
        $constants = $class->getConstants();

        $list = array();
        $type = strtoupper($type) . '_';
        foreach ($constants as $key => $value) {
            if (strpos($key, $type) === 0) {
                $list[$key] = $value;
            }
        }
        return $list;
    }

    /**
     * Returns an array notation of current instance
     *
     * @return array
     */
    public function toArray()
    {
        $rc = new \ReflectionClass(get_class($this));
        $properties = $rc->getProperties();
        $arrResult = array();

        foreach ($properties as $rp) {
            /* @var $rp \ReflectionProperty */
            $getter = $this->determinePropertyGetter($rp->getName());
            if (!method_exists($this, $getter)) {
                continue;
            }
            $arrResult[$rp->getName()] = $this->$getter();
        }

        return $arrResult;
    }
}
