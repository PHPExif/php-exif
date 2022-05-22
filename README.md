# [PHPExif v0.7.5](http://github.com/LycheeOrg/php-exif)

[![GitHub Release][release-shield]](https://github.com/LycheeOrg/php-exif/releases)
[![PHP 8.0 & 8.1][php-shield]](https://github.com/LycheeOrg/php-exif#installation-composer)
[![MIT License][license-shield]](https://github.com/LycheeOrg/php-exif/blob/master/LICENSE)
<br>
[![Build Status](https://github.com/LycheeOrg/php-exif/workflows/Tests/badge.svg)](https://github.com/LycheeOrg/php-exif/actions)
[![Coverage Status](https://codecov.io/gh/LycheeOrg/php-exif/branch/master/graph/badge.svg)](https://codecov.io/gh/LycheeOrg/php-exif)
[![Code Climate](https://api.codeclimate.com/v1/badges/f15042d535274f36c5a2/maintainability)](https://codeclimate.com/github/LycheeOrg/php-exif/maintainability)

PHPExif is a library which gives you easy access to the EXIF meta-data of an image or video.

PHPExif serves as a wrapper around some native or CLI tools which access this EXIF meta-data from an image video. As such, it provides a standard API for retrieving and accessing that information.

## Supported tools

* Native PHP functionality (exif_read_data, iptcparse) [images]
* [Exiftool](http://www.sno.phy.queensu.ca/~phil/exiftool) adapter (wrapper for the exiftool binary) [images and videos]
* [FFmpeg/FFprobe](https://ffmpeg.org) adapter (wrapper for the exiftool binary) [videos]
* [Imagick](https://www.php.net/manual/de/book.imagick.php) adapter [images]

## Installation (composer)

```sh
composer require lychee-org/php-exif
```


## Usage

[v0.3.0+](Resources/doc/usage.md)

## Contributing

Please submit all pull requests against the correct branch. The release branch for the next version is a branch with the same name as the next version. Bugfixes should go in the master branch, unless they are for code in a new release branch.

PHPExif is written according the [PSR-0/1/2 standards](http://www.php-fig.org/). When submitting code, please make sure it is conform these standards.
We aim to have all functionality covered by unit tests. When submitting code, you are strongly encouraged to unit test your code and to keep the level of code coverage on par with the current level.

All contributions are welcomed and greatly appreciated.

## Feedback

Have a bug or a feature request? [Please open a new issue](https://github.com/LycheeOrg/php-exif/issues). Before opening any issue, please search for existing issues.

## Contributors

* [Tom Van Herreweghe](http://github.com/Miljar)
* [Ingewikkeld](https://github.com/Ingewikkeld)
* [Christophe Singer](https://github.com/wasinger)
* [Hanov Ruslan](https://github.com/hanovruslan)
* [Julian Gutierrez](https://github.com/juliangut)
* [Marek Szymczuk](https://github.com/bonzai)
* [Scott Pringle](https://github.com/Luciam91)
* [tmp-hallenser](https://github.com/tmp-hallenser)
* [kamil4](https://github.com/kamil4)
* [ildyria](https://github.com/ildyria)
* [nagmat84](https://github.com/nagmat84)

## License

[MIT License](http://github.com/LycheeOrg/php-exif/blob/master/LICENSE)


[release-shield]: https://img.shields.io/github/release-pre/LycheeOrg/php-exif.svg
[php-shield]: https://img.shields.io/badge/PHP-8.0%20|%208.1-blue
[license-shield]: https://img.shields.io/github/license/LycheeOrg/Lychee.svg
