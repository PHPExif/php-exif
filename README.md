# [PHPExif v0.2.1](http://github.com/Miljar/php-exif) [![Build Status](https://travis-ci.org/Miljar/php-exif.png?branch=master)](https://travis-ci.org/Miljar/php-exif) [![Coverage Status](https://coveralls.io/repos/Miljar/php-exif/badge.png)](https://coveralls.io/r/Miljar/php-exif)

PHPExif is a library which gives you easy access to the EXIF meta-data of an image.

PHPExif serves as a wrapper around some native or CLI tools which access this EXIF meta-data from an image. As such, it provides a standard API for retrieving and accessing that information.

## Supported tools

* Native PHP functionality (exif_read_data, iptcparse)
* [Exiftool](http://www.sno.phy.queensu.ca/~phil/exiftool/‎) adapter (wrapper for the exiftool binary)

## Installation (composer)

```json
"miljar/php-exif": "0.*"
```


## Usage

### Using factory method

```php
<?php
// reader with Native adapter
$reader = \PHPExif\Reader::factory(\PHPExif\Reader::TYPE_NATIVE);

// reader with Exiftool adapter
//$reader = \PHPExif\Reader::factory(\PHPExif\Reader::TYPE_EXIFTOOL);

$exif = $reader->getExifFromFile('/path/to/file');

echo 'Title: ' . $exif->getTitle() . PHP_EOL;
```

### Using custom options

```php
<?php
$adapter = new \PHPExif\Reader\Adapter\Exiftool(
    array(
        'toolPath'  => '/path/to/exiftool',
    )
);
$reader = new \PHPExif\Reader($adapter);

$exif = $reader->getExifFromFile('/path/to/file');

echo 'Title: ' . $exif->getTitle() . PHP_EOL;
```

## Contributing

Please submit all pull requests against the correct branch. The release branch for the next version is a branch with the same name as the next version. Bugfixes should go in the master branch, unless they are for code in a new release branch.

PHPExif is written according the [PSR-0/1/2 standards](http://www.php-fig.org/‎). When submitting code, please make sure it is conform these standards.

All contributions are welcomed and greatly appreciated.

## Feedback

Have a bug or a feature request? [Please open a new issue](https://github.com/Miljar/php-exif/issues). Before opening any issue, please search for existing issues.

## License

[MIT License](http://github.com/Miljar/php-exif/blob/master/LICENSE)
