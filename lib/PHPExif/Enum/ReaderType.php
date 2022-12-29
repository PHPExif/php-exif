<?php
/**
 * @codeCoverageIgnore
 *
 * Traits don't have implementation.
 */

namespace PHPExif\Enum;

enum ReaderType: string
{
    case NATIVE   = 'native';
    case EXIFTOOL = 'exiftool';
    case FFPROBE  = 'ffprobe';
    case IMAGICK  = 'imagick';
}
