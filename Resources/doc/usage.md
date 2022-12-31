## Usage ##

### Using factory method

```php
<?php
// reader with Native adapter
$reader = \PHPExif\Reader\Reader::factory(\PHPExif\Enum\ReaderType::NATIVE);

// reader with Exiftool adapter
//$reader = \PHPExif\Reader\Reader::factory(\PHPExif\Enum\ReaderType::EXIFTOOL);

// reader with FFmpeg/FFprobe adapter
//$reader = \PHPExif\Reader\Reader::factory(\PHPExif\Enum\ReaderType::FFPROBE);

// reader with Imagick adapter
//$reader = \PHPExif\Reader\Reader::factory(\PHPExif\Enum\ReaderType::IMAGICK);

$exif = $reader->read('/path/to/file');

echo 'Title: ' . $exif->getTitle() . PHP_EOL;
```

### Using custom options

```php
<?php
$adapter = new \PHPExif\Adapter\Exiftool(
    array(
        'toolPath'  => '/path/to/exiftool',
    )
);
$reader = new \PHPExif\Reader\Reader($adapter);

$exif = $reader->read('/path/to/file');

echo 'Title: ' . $exif->getTitle() . PHP_EOL;
```
