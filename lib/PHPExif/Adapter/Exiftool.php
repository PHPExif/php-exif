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
use DateTime;

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
     *
     * @var string
     */
    protected $toolPath;

    /**
     * @var boolean
     */
    protected $numeric = true;

    /**
     * Setter for the exiftool binary path
     *
     * @param string $path The path to the exiftool binary
     * @return \PHPExif\Adapter\Exiftool Current instance
     * @throws \InvalidArgumentException When path is invalid
     */
    public function setToolPath($path)
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
    public function setNumeric($numeric)
    {
        $this->numeric = $numeric;
    }

    /**
     * Getter for the exiftool binary path
     * Lazy loads the "default" path
     *
     * @return string
     */
    public function getToolPath()
    {
        if (empty($this->toolPath)) {
            $path = exec('which ' . self::TOOL_NAME);
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
    public function getExifFromFile($file)
    {
        $result = $this->getCliOutput(
            sprintf(
                '%1$s%3$s -j %2$s',
                $this->getToolPath(),
                $file,
                $this->numeric ? ' -n' : ''
            )
        );

        $data = json_decode($result, true);
        $mappedData = $this->mapData(reset($data));
        $exif = new Exif($mappedData);
        $exif->setRawData(reset($data));

        return $exif;
    }

    /**
     * Returns the output from given cli command
     *
     * @param string $command
     * @return mixed
     * @throws RuntimeException If the command can't be executed
     */
    protected function getCliOutput($command)
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

    /**
     * Maps native data to Exif format
     *
     * @param array $source
     * @return array
     */
    public function mapData(array $source)
    {
        $focalLength = false;
        if (isset($source['FocalLength'])) {
            $focalLengthParts = explode(' ', $source['FocalLength']);
            $focalLength = (int) reset($focalLengthParts);
        }

        $exposureTime = false;
        if (isset($source['ExposureTime'])) {
            $exposureTime = '1/' . round(1 / $source['ExposureTime']);
        }

        $caption = false;
        if (isset($source['Caption'])) {
            $caption = $source['Caption'];
        } elseif (isset($source['Caption-Abstract'])) {
            $caption = $source['Caption-Abstract'];
        }

        return array(
            Exif::APERTURE              => (!isset($source['Aperture'])) ?
                false : sprintf('f/%01.1f', $source['Aperture']),
            Exif::AUTHOR                => (!isset($source['Artist'])) ? false : $source['Artist'],
            Exif::CAMERA                => (!isset($source['Model'])) ? false : $source['Model'],
            Exif::CAPTION               => $caption,
            Exif::COLORSPACE            => (!isset($source[Exif::COLORSPACE]) ? false : $source[Exif::COLORSPACE]),
            Exif::COPYRIGHT             => (!isset($source['Copyright'])) ? false : $source['Copyright'],
            Exif::CREATION_DATE         => (!isset($source['CreateDate'])) ?
                false : DateTime::createFromFormat('Y:m:d H:i:s', $source['CreateDate']),
            Exif::CREDIT                => (!isset($source['Credit'])) ? false : $source['Credit'],
            Exif::EXPOSURE              => $exposureTime,
            Exif::FILESIZE              => (!isset($source[Exif::FILESIZE]) ? false : $source[Exif::FILESIZE]),
            Exif::FOCAL_LENGTH          => $focalLength,
            Exif::FOCAL_DISTANCE        => (!isset($source['ApproximateFocusDistance'])) ?
                false : sprintf('%1$sm', $source['ApproximateFocusDistance']),
            Exif::HEADLINE              => (!isset($source['Headline'])) ? false : $source['Headline'],
            Exif::HEIGHT                => (!isset($source['ImageHeight'])) ? false : $source['ImageHeight'],
            Exif::HORIZONTAL_RESOLUTION => (!isset($source['XResolution'])) ? false : $source['XResolution'],
            Exif::ISO                   => (!isset($source['ISO'])) ? false : $source['ISO'],
            Exif::JOB_TITLE             => (!isset($source['JobTitle'])) ? false : $source['JobTitle'],
            Exif::KEYWORDS              => (!isset($source['Keywords'])) ? false : $source['Keywords'],
            Exif::MIMETYPE              => (!isset($source['MIMEType'])) ? false : $source['MIMEType'],
            Exif::ORIENTATION           => (!isset($source['Orientation'])) ? false : $source['Orientation'],
            Exif::SOFTWARE              => (!isset($source['Software'])) ? false : $source['Software'],
            Exif::SOURCE                => (!isset($source['Source'])) ? false : $source['Source'],
            Exif::TITLE                 => (!isset($source['Title'])) ? false : $source['Title'],
            Exif::VERTICAL_RESOLUTION   => (!isset($source['YResolution'])) ? false : $source['YResolution'],
            Exif::WIDTH                 => (!isset($source['ImageWidth'])) ? false : $source['ImageWidth'],
        );
    }
}
