<?php

use PHPExif\Adapter\Exiftool;

/**
 * @covers Exiftool::<!public>
 */
class ExiftoolTest extends \PHPUnit\Framework\TestCase
{
    protected Exiftool $adapter;

    public function setUp(): void
    {
        $this->adapter = new Exiftool();
    }

    /**
     * @group exiftool
     * @covers Exiftool::getToolPath
     */
    public function testGetToolPathFromProperty()
    {
        $reflProperty = new \ReflectionProperty(Exiftool::class, 'toolPath');
        $reflProperty->setAccessible(true);
        $expected = '/foo/bar/baz';
        $reflProperty->setValue($this->adapter, $expected);

        $this->assertEquals($expected, $this->adapter->getToolPath());
    }

    /**
     * @group exiftool
     * @covers Exiftool::setToolPath
     */
    public function testSetToolPathInProperty()
    {
        $reflProperty = new \ReflectionProperty(Exiftool::class, 'toolPath');
        $reflProperty->setAccessible(true);

        $expected = '/tmp';
        $this->adapter->setToolPath($expected);

        $this->assertEquals($expected, $reflProperty->getValue($this->adapter));
    }

    /**
     * @group exiftool
     * @covers Exiftool::setToolPath
     */
    public function testSetToolPathThrowsException()
    {
        $this->expectException('InvalidArgumentException');
        $this->adapter->setToolPath('/foo/bar');
    }


    /**
     * @group exiftool
     * @covers Exiftool::getToolPath
     */
    public function testGetToolPathLazyLoadsPath()
    {
        $this->assertIsString($this->adapter->getToolPath());
    }

    /**
     * @group exiftool
     * @covers Exiftool::setNumeric
     */
    public function testSetNumericInProperty()
    {
        $reflProperty = new \ReflectionProperty(Exiftool::class, 'numeric');
        $reflProperty->setAccessible(true);

        $expected = true;
        $this->adapter->setNumeric($expected);

        $this->assertEquals($expected, $reflProperty->getValue($this->adapter));
    }

    /**
     * @see URI http://www.sno.phy.queensu.ca/~phil/exiftool/faq.html#Q10
     * @group exiftool
     * @covers Exiftool::setEncoding
     */
    public function testSetEncodingInProperty()
    {
        $reflProperty = new \ReflectionProperty(Exiftool::class, 'encoding');
        $reflProperty->setAccessible(true);

        $expected = array('iptc' => 'cp1250');
        $input = array('iptc' => 'cp1250', 'exif' => 'utf8', 'foo' => 'bar');
        $this->adapter->setEncoding($input);

        $this->assertEquals($expected, $reflProperty->getValue($this->adapter));
    }

    /**
     * @group exiftool
     * @covers Exiftool::getExifFromFile
     */
    public function testGetExifFromFile()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/morning_glory_pool_500.jpg';
        $this->adapter->setOptions(array('encoding' => array('iptc' => 'cp1252')));
        $result = $this->adapter->getExifFromFile($file);
        $this->assertInstanceOf('\PHPExif\Exif', $result);
        $this->assertIsArray($result->getRawData());
        $this->assertNotEmpty($result->getRawData());
    }

    /**
     * @group exiftool
     * @covers Exiftool::getExifFromFile
     */
    public function testGetExifFromFileWithUtf8()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/utf8.jpg';
        $this->adapter->setOptions(array('encoding' => array('iptc' => 'utf8')));
        $result = $this->adapter->getExifFromFile($file);
        $this->assertInstanceOf('\PHPExif\Exif', $result);
        $this->assertIsArray($result->getRawData());
        $this->assertNotEmpty($result->getRawData());
    }

    /**
     * @group exiftool
     * @covers Exiftool::getCliOutput
     */
    public function testGetCliOutput()
    {
        $reflMethod = new \ReflectionMethod(Exiftool::class, 'getCliOutput');
        $reflMethod->setAccessible(true);

        $result = $reflMethod->invoke(
            $this->adapter,
            sprintf(
                '%1$s',
                'pwd'
            )
        );

        $this->assertIsString($result);
    }
}
