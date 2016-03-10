<?php

namespace Tests\PHPExif\Adapter\Native;

use Mockery as m;
use PHPExif\Adapter\Native\Mapper;
use PHPExif\Data\Exif;
use PHPExif\Data\Iptc;
use PHPExif\Data\Metadata;
use PHPExif\Mapper\ArrayMapper;
use PHPExif\Mapper\FieldMapper;

/**
 * @coversDefaultClass \PHPExif\Adapter\Native\Mapper
 * @covers ::<!public>
 */
class MapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group mapper
     *
     * @return void
     */
    public function testIfClassImplementsInterface()
    {
        $mock = m::mock(Mapper::class);
        $this->assertInstanceOf(
            ArrayMapper::class,
            $mock
        );
    }

    /**
     * @covers ::registerFieldMappers
     * @group mapper
     *
     * @return void
     */
    public function testRegisterFieldMappersCallsInternalMethod()
    {
        $fieldMappers = array(
            m::mock(FieldMapper::class),
            m::mock(FieldMapper::class),
            m::mock(FieldMapper::class),
            m::mock(FieldMapper::class),
        );

        $ctr = 0;
        $mock = m::mock(Mapper::class . '[registerFieldMapper]');
        $mock->shouldReceive('registerFieldMapper')
            ->with(
                m::on(
                    function ($arg) use ($fieldMappers, &$ctr) {
                        $result = false;
                        if ($arg === $fieldMappers[$ctr]) {
                            $result = true;
                        }

                        $ctr++;

                        return $result;
                    }
                )
            )
            ->times(count($fieldMappers))
            ->andReturn(null);

        $mock->registerFieldMappers($fieldMappers);
    }

    /**
     * @covers ::registerFieldMapper
     * @group mapper
     *
     * @return void
     */
    public function testRegisterFieldMapperAddsToList()
    {
        $reflField = new \ReflectionProperty(
            Mapper::class,
            'fieldMappers'
        );
        $reflField->setAccessible(true);

        $mock = m::mock(Mapper::class)->makePartial();

        $this->assertCount(
            0,
            $reflField->getValue($mock)
        );

        $field = 'foo';
        $fieldMapper = m::mock(FieldMapper::class . '[getSupportedFields]');
        $fieldMapper->shouldReceive('getSupportedFields')
            ->once()
            ->andReturn(array($field));

        $mock->registerFieldMapper($fieldMapper);

        $this->assertCount(
            1,
            $reflField->getValue($mock)
        );
    }

    /**
     * @covers ::registerFieldMapper
     * @group mapper
     *
     * @return void
     */
    public function testRegisterFieldMapperAllowsOverwritingExisting()
    {
        $mock = m::mock(Mapper::class)->makePartial();

        $field = 'foo';
        $fieldMapper = m::mock(FieldMapper::class . '[getSupportedFields]');
        $fieldMapper->shouldReceive('getSupportedFields')
            ->once()
            ->andReturn(array($field));

        $fieldMapper2 = clone $fieldMapper;

        $mock->registerFieldMapper($fieldMapper);
        $mock->registerFieldMapper($fieldMapper2);

        $result = $mock->getFieldMapper($field);
        $this->assertSame($fieldMapper2, $result);
    }

    /**
     * @covers ::getFieldMapper
     * @group mapper
     *
     * @return void
     */
    public function testGetFieldMapperReturnsFromList()
    {
        $field = 'foo';
        $mock = m::mock(Mapper::class)->makePartial();
        $fieldMapper = m::mock(FieldMapper::class);
        $reflField = new \ReflectionProperty(
            Mapper::class,
            'fieldMappers'
        );
        $reflField->setAccessible(true);
        $reflField->setValue($mock, array($field => $fieldMapper,));

        $actual = $mock->getFieldMapper($field);

        $this->assertSame(
            $fieldMapper,
            $actual
        );
    }

    /**
     * @covers ::getFieldMapper
     * @group mapper
     * @expectedException \PHPExif\Exception\Mapper\MapperNotRegisteredException
     *
     * @return void
     */
    public function testGetFieldMapperThrowsExceptionForUnknownMapper()
    {
        $field = 'foo';
        $mock = m::mock(Mapper::class)->makePartial();
        $mock->getFieldMapper($field);
    }

    /**
     * @covers ::mapperRegisteredForField
     * @group mapper
     *
     * @return void
     */
    public function testMapperRegisteredForFieldCorrectlyDeterminesIfMapperIsRegistered()
    {
        $field = 'foo';
        $mock = m::mock(Mapper::class)->makePartial();
        $fieldMapper = m::mock(FieldMapper::class . '[getSupportedFields]');
        $fieldMapper->shouldReceive('getSupportedFields')
            ->once()
            ->andReturn(array($field));
        $reflField = new \ReflectionProperty(
            Mapper::class,
            'fieldMappers'
        );
        $reflField->setAccessible(true);
        $reflField->setValue($mock, array($field => $fieldMapper,));

        $this->assertTrue(
            $mock->mapperRegisteredForField($field)
        );
        $this->assertFalse(
            $mock->mapperRegisteredForField('bar')
        );
    }

    /**
     * @covers ::map
     * @group mapper
     *
     * @return void
     */
    public function testMapForwardsCall()
    {
        $data = array();

        $mock = m::mock(Mapper::class . '[mapArray]')->makePartial();
        $mock->shouldReceive('mapArray')
            ->once()
            ->with(
                $data,
                m::type(Metadata::class)
            )
            ->andReturnNull();

        $actual = $mock->map($data);

        $this->assertInstanceOf(
            Metadata::class,
            $actual
        );
    }

    /**
     * @covers ::mapArray
     * @group mapper
     *
     * @return void
     */
    public function testMapArrayForwardsCall()
    {
        $exif = new Exif(array());
        $iptc = new Iptc(array());

        $input = array();
        $metadata = new Metadata;
        $output = m::mock($metadata);
        $output->shouldReceive('getExif')
            ->once()
            ->andReturn($exif);
        $output->shouldReceive('getIptc')
            ->once()
            ->andReturn($iptc);

        $mapper = new Mapper;

        foreach (array(Mapper::FIELD_EXIF, MAPPER::FIELD_IPTC) as $field) {
            $fieldMapper = m::mock(FieldMapper::class . '[getSupportedFields,mapArray]');
            $fieldMapper->shouldReceive('getSupportedFields')
                ->once()
                ->andReturn(array($field));
            $fieldMapper->shouldReceive('mapArray')
                ->once()
                ->with(
                    $input,
                    ($field === Mapper::FIELD_EXIF) ? $exif : $iptc
                )
                ->andReturnNull();

            $mapper->registerFieldMapper($fieldMapper);
        }

        $mapper->mapArray($input, $output);
    }
}
