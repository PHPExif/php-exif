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

namespace PHPExif\Reader\Adapter;

use PHPExif\Reader\AdapterAbstract;
use PHPExif\Exif;
use \InvalidArgumentException;
use \RuntimeException;

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
     * Setter for the exiftool binary path
     * 
     * @param string $path The path to the exiftool binary
     * @return \PHPExif\Reader\Adapter\Exiftool Current instance
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
                '%1$s -j %2$s',
                $this->getToolPath(),
                $file
            )
        );
        
        $data = json_decode($result);
        var_dump($data);
        die();
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
}
