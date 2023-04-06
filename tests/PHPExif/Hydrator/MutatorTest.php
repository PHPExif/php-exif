<?php

use PhpExif\Hydrator\Mutator;

class MutatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup function before the tests
     */
    protected function setUp(): void
    {
    }

    /**
     * @group hydrator
     */
    public function testHydrateCallsDetermineMutator()
    {
        // input data
        $input = array(
            'foo' => 'bar',
        );

        // create mock
        $mock = $this->getMockBuilder(Mutator::class)
            ->onlyMethods(array('determineMutator'))
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
        $hydrator = new Mutator();
        $hydrator->hydrate($mock, $input);
    }

    /**
     * @group hydrator
     */
    public function testHydrateCallsEmptyValues()
    {
        // input data
        $input = array(
            'foo' => null,
            'bar' => '',
        );

        // create mock
        $mock = $this->getMockBuilder('TestClass')
            ->onlyMethods(array('setFoo', 'setBar'))
            ->getMock();

        $mock->expects($this->exactly(0))
            ->method('setFoo');
        $mock->expects($this->exactly(0))
            ->method('setBar');

        // do the test
        $hydrator = new Mutator();
        $hydrator->hydrate($mock, $input);
    }
}

class TestClass
{
    public function setFoo()
    {
    }

    public function setBar()
    {
    }
}
