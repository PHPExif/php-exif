<?php
/**
 * @covers \PHPExif\Hydrator\Mutator::<!public>
 * @covers \PHPExif\Hydrator\HydratorInterface
 */
class MutatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup function before the tests
     */
    public function setUp()
    {
    }

    /**
     * @group hydrator
     * @covers \PHPExif\Hydrator\Mutator::hydrate
     */
    public function testHydrateCallsDetermineMutator()
    {
        // input data
        $input = array(
            'foo' => 'bar',
        );

        // create mock
        $mock = $this->getMockBuilder('\\PHPExif\\Hydrator\\Mutator')
            ->setMethods(array('determineMutator'))
            ->getMock();

        $mock->expects($this->exactly(count($input)))
            ->method('determineMutator')
            ->will($this->returnValue('setFoo'));

        $object = new TestClass();

        // do the test
        $mock->hydrate($object, $input);
    }

    /**
     * @group hydrator
     * @covers \PHPExif\Hydrator\Mutator::hydrate
     */
    public function testHydrateCallsMutatorsOnObject()
    {
        // input data
        $input = array(
            'bar' => 'baz',
        );

        // create mock
        $mock = $this->getMockBuilder('TestClass')
            ->setMethods(array('setBar'))
            ->getMock();

        $mock->expects($this->once())
            ->method('setBar')
            ->with($this->equalTo($input['bar']));

        // do the test
        $hydrator = new \PHPExif\Hydrator\Mutator;
        $hydrator->hydrate($mock, $input);
    }
}

class TestClass
{
    public function setBar()
    {
    }
}

