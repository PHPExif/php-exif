<?php

namespace Tests\PHPExif\Exception;

use Mockery as m;
use PHPExif\AbstractCollection;

/**
 * Class: AbstractCollectionTest
 *
 * @see \PHPUnit_Framework_TestCase
 * @abstract
 * @coversDefaultClass \PHPExif\AbstractCollection
 * @covers ::<!public>
 */
class AbstractCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @group collection
     *
     * @return void
     */
    public function testConstructorAddsElementsFromParameter()
    {
        $input = array(
            'foo' => 'bar',
            'baz' => 'quux',
        );
        $ctr = array(
            'key' => 0,
            'value' => 0,
        );

        $checker = function ($type) use ($input, &$ctr) {
            return function ($arg) use ($input, $type, &$ctr) {
                $result = false;

                $data = null;
                if ($type === 'key') {
                    $data = array_keys($input);
                } else {
                    $data = array_values($input);
                }

                if ($arg === $data[$ctr[$type]]) {
                    $result = true;
                }

                $ctr[$type]++;

                return $result;
            };
        };

        $mock = m::mock(
            AbstractCollection::class
        )->shouldDeferMissing();
        $mock->shouldReceive('add')
            ->with(
                m::on(
                    $checker('key')
                ),
                m::on(
                    $checker('value')
                )
            )
            ->andReturnNull();

        $mock->__construct($input);

        $this->assertEquals(
            count($input),
            $ctr['key']
        );
        $this->assertEquals(
            count($input),
            $ctr['value']
        );
    }
}
