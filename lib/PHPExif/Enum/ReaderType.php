<?php
/**
 * @codeCoverageIgnore
 *
 * Traits don't have implementation.
 */

namespace PHPExif\Enum;

enum ReaderType
{
    case NATIVE;
    case EXIFTOOL;
    case FFPROBE;
    case IMAGICK;
}
