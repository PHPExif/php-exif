<?php
/**
 * PHP Exif Exiftool Reader Adapter
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Reader
 */

namespace PHPExif\Adapter;

use PHPExif\Exif;
use InvalidArgumentException;
use RuntimeException;

/**
 * PHP Exif Exiftool Reader Adapter
 *
 * Uses native PHP functionality to read data from a file
 *
 * @category    PHPExif
 * @package     Reader
 */
class Exiftool extends AdapterAbstract
{
    const TOOL_NAME = 'exiftool';

    /**
     * Path to the exiftool binary
     */
    protected string $toolPath = '';
    protected bool $numeric = true;
    protected array $encoding = array();
    protected string $mapperClass = '\\PHPExif\\Mapper\\Exiftool';

    /**
     * Setter for the exiftool binary path
     *
     * @param string $path The path to the exiftool binary
     * @return \PHPExif\Adapter\Exiftool Current instance
     * @throws \InvalidArgumentException When path is invalid
     */
    public function setToolPath(string $path) : Exiftool
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Given path (%1$s) to the exiftool binary is invalid',
                    $path
                )
            );
        }

        $this->toolPath = $path;

        return $this;
    }

    /**
     * @param boolean $numeric
     */
    public function setNumeric(bool $numeric) : void
    {
        $this->numeric = $numeric;
    }

    /**
     * @see  http://www.sno.phy.queensu.ca/~phil/exiftool/faq.html#Q10
     * @param array $encoding encoding parameters in an array eg. ["exif" => "UTF-8"]
     */
    public function setEncoding(array $encoding) : void
    {
        $possible_keys = array("exif", "iptc", "id3", "photoshop", "quicktime",);
        $possible_values = array("UTF8", "cp65001", "UTF-8", "Thai", "cp874", "Latin", "cp1252",
            "Latin1", "MacRoman", "cp10000", "Mac", "Roman", "Latin2", "cp1250", "MacLatin2",
            "cp10029", "Cyrillic", "cp1251", "Russian", "MacCyrillic", "cp10007", "Greek",
            "cp1253", "MacGreek", "cp10006", "Turkish", "cp1254", "MacTurkish", "cp10081",
            "Hebrew", "cp1255", "MacRomanian", "cp10010", "Arabic", "cp1256", "MacIceland",
            "cp10079", "Baltic", "cp1257", "MacCroatian", "cp10082", "Vietnam", "cp1258",);
        foreach ($encoding as $type => $encoding) {
            if (in_array($type, $possible_keys) && in_array($encoding, $possible_values)) {
                $this->encoding[$type] = $encoding;
            }
        }
    }

    /**
     * Getter for the exiftool binary path
     * Lazy loads the "default" path
     *
     * @return string
     */
    public function getToolPath() : string
    {
        if (empty($this->toolPath)) {
            // Do not use "which": not available on sh
            $path = exec('command -v ' . self::TOOL_NAME);
            // $path = exec('which ' . self::TOOL_NAME);
            $this->setToolPath($path);
        }

        return $this->toolPath;
    }

    /**
     * Reads & parses the EXIF data from given file
     *
     * @param string $file
     * @return \PHPExif\Exif Instance of Exif object with data
     * @throws \RuntimeException If the EXIF data could not be read
     */
    public function getExifFromFile(string $file) : Exif
    {
        $encoding = '';
        if (!empty($this->encoding)) {
            $encoding = '-charset ';
            foreach ($this->encoding as $key => $value) {
                $encoding .= escapeshellarg($key).'='.escapeshellarg($value);
            }
        }
        /**
         * @var string
         */
        $result = $this->getCliOutput(
            sprintf(
                '%1$s%3$s -j -a -G1 %5$s -c %4$s %2$s',
                $this->getToolPath(),
                escapeshellarg($file),
                $this->numeric ? ' -n' : '',
                escapeshellarg('%d deg %d\' %.4f"'),
                $encoding
            )
        );

        /**
         * @var string
         */
        $result = $this->convertToUTF8($result);

        $data = json_decode($result, true);
        if (!is_array($data)) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException(
                'Could not decode exiftool output'
            );
            // @codeCoverageIgnoreEnd
        }

        // map the data:
        /**
         * @var \PHPExif\Mapper\Exiftool
         */
        $mapper = $this->getMapper();
        $mapper->setNumeric($this->numeric);
        $mappedData = $mapper->mapRawData(reset($data));

        // hydrate a new Exif object
        $exif = new Exif();
        $hydrator = $this->getHydrator();
        $hydrator->hydrate($exif, $mappedData);
        $exif->setRawData(reset($data));

        return $exif;
    }

    /**
     * Returns the output from given cli command
     *
     * @param string $command
     * @return string|false
     * @throws RuntimeException If the command can't be executed
     */
    protected function getCliOutput(string $command) : string|false
    {
        $descriptorspec = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'a')
        );

        $process = proc_open($command, $descriptorspec, $pipes);

        if (!is_resource($process)) {
            throw new RuntimeException(
                'Could not open a resource to the exiftool binary'
            );
        }

        $result = stream_get_contents($pipes[1]);
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        proc_close($process);

        return $result;
    }
}
