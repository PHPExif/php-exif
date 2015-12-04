<?php
/**
 * @covers \PHPExif\Reader::<!public>
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group reader
     * @covers \PHPExif\Reader::__construct
     */
    public function testConstructorCallsSetData()
    {
        $mock = $this->getMockBuilder('\\PHPExif\\Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $adapter = $this->getMockBuilder('\\PHPExif\\Adapter\\ReaderInterface')
            ->getMock();

        // now call the constructor
        $reflectedClass = new ReflectionClass('\\PHPExif\\Reader');
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($mock, array($adapter));

        // verify if set in property
        $reflProperty = new \ReflectionProperty('\\PHPExif\\Reader', 'adapter');
        $reflProperty->setAccessible(true);

        $actual = $reflProperty->getValue($mock);

        $this->assertSame(
            $adapter,
            $actual
        );
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::getAdapter
     */
    public function testGetAdapterReturnsFromProperty()
    {
        $adapter = $this->getMockBuilder('\\PHPExif\\Adapter\\ReaderInterface')
            ->getMock();
        $reader = new \PHPExif\Reader($adapter);

        $reflProperty = new \ReflectionProperty('\\PHPExif\\Reader', 'adapter');
        $reflProperty->setAccessible(true);

        $relfProperty->setValue($reader, $adapter);

        $this->assertSame(
            $adapter,
            $reader->getAdapter()
        );
    }
}
