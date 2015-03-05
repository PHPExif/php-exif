<?php

namespace PHPExif\Reader;

interface ReaderInterface
{
    /**
     * Reads & parses the EXIF data from given file
     *
     * @param string $file
     * @return \PHPExif\Exif Instance of Exif object with data
     */
    public function read($file);
}
