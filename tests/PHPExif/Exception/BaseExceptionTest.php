<?php

namespace Tests\PHPExif\Exception;

/**
 * Class: BaseExceptionTest
 *
 * @see \PHPUnit_Framework_TestCase
 * @abstract
 */
abstract class BaseExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Asserts that static $method on given $classname with given
     * $arguments returns an instance of that $classname
     *
     * @param string $classname
     * @param string $method
     * @param array $arguments
     *
     * @return void
     */
    public function assertNamedConstructorReturnsInstance(
        $classname,
        $method,
        array $arguments
    ) {
        $result = forward_static_call_array(
            array(
                $classname,
                $method
            ),
            $arguments
        );

        $this->assertInstanceOf(
            $classname,
            $result
        );
    }
}
