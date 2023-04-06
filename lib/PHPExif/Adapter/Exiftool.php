<?php

namespace PHPExif\Adapter;

use PHPExif\Exif;
use InvalidArgumentException;
use PHPExif\Mapper\Exiftool as MapperExiftool;
use PHPExif\Reader\PhpExifReaderException;
use Safe\Exceptions\ExecException;

use Safe\Exceptions\JsonException;

use function Safe\exec;
use function Safe\json_decode;
use function Safe\stream_get_contents;
use function Safe\fclose;

/**
 * PHP Exif Exiftool Reader Adapter
 *
 * Uses native PHP functionality to read data from a file
 *
 * @category    PHPExif
 * @package     Reader
 */
class Exiftool extends AbstractAdapter
{
    public const TOOL_NAME = 'exiftool';

    /**
     * Path to the exiftool binary
     */
    protected string $toolPath = '';
    protected bool $numeric = true;
    protected array $encoding = array();
    protected string $mapperClass = MapperExiftool::class;

    /**
     * Setter for the exiftool binary path
     *
     * @param string $path The path to the exiftool binary
     * @return \PHPExif\Adapter\Exiftool Current instance
     * @throws \InvalidArgumentException When path is invalid
     */
    public function setToolPath(string $path): Exiftool
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
    public function setNumeric(bool $numeric): void
    {
        $this->numeric = $numeric;
    }

    /**
     * @see  http://www.sno.phy.queensu.ca/~phil/exiftool/faq.html#Q10
     * @param array $encodings encoding parameters in an array eg. ["exif" => "UTF-8"]
     */
    public function setEncoding(array $encodings): void
    {
        $possible_keys = array("exif", "iptc", "id3", "photoshop", "quicktime",);
        $possible_values = array("UTF8", "cp65001", "UTF-8", "Thai", "cp874", "Latin", "cp1252",
            "Latin1", "MacRoman", "cp10000", "Mac", "Roman", "Latin2", "cp1250", "MacLatin2",
            "cp10029", "Cyrillic", "cp1251", "Russian", "MacCyrillic", "cp10007", "Greek",
            "cp1253", "MacGreek", "cp10006", "Turkish", "cp1254", "MacTurkish", "cp10081",
            "Hebrew", "cp1255", "MacRomanian", "cp10010", "Arabic", "cp1256", "MacIceland",
            "cp10079", "Baltic", "cp1257", "MacCroatian", "cp10082", "Vietnam", "cp1258",);
        foreach ($encodings as $type => $encoding) {
            if (in_array($type, $possible_keys, true) && in_array($encoding, $possible_values, true)) {
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
    public function getToolPath(): string
    {
        if ($this->toolPath === '') {
            try {
                // Do not use "which": not available on sh
                $path = exec('command -v ' . self::TOOL_NAME);
                $this->setToolPath($path);
            } catch (ExecException) {
                // Do nothing
            }
        }

        return $this->toolPath;
    }

    /**
     * Reads & parses the EXIF data from given file
     *
     * @param string $file
     * @return Exif Instance of Exif object with data
     * @throws PhpExifReaderException If the EXIF data could not be read
     */
    public function getExifFromFile(string $file): Exif
    {
        $encoding = '';
        if (count($this->encoding) > 0) {
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
         * @var string $result
         */
        $result = $this->convertToUTF8($result);

        try {
            $data = json_decode($result, true);
        } catch (JsonException $e) {
            // @codeCoverageIgnoreStart
            $data = false;
            // @codeCoverageIgnoreStart
        }
        if (!is_array($data)) {
            // @codeCoverageIgnoreStart
            throw new PhpExifReaderException(
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
     * @throws PhpExifReaderException If the command can't be executed
     */
    protected function getCliOutput(string $command): string|false
    {
        $descriptorspec = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'a')
        );

        $process = proc_open($command, $descriptorspec, $pipes);

        if (!is_resource($process)) {
            throw new PhpExifReaderException(
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
