# Contributing

## Branching strategy

PHPExif uses the [Git Flow](https://github.com/nvie/gitflow) branching strategy.

TL;DR:

- Work on a new version is merged in the `develop` branch
- Current stable release is in the `master` branch
- Hotfixes: branch from `master`
- New feature: branch from `develop`
- Bugfix for older MAJOR version: branch from MAJOR release branch

## Coding Standards

PHPExif is written according the [PSR-0/1/2 standards](http://www.php-fig.org/). When submitting code, please make sure it is conform these standards.

## Testing

PHPExif is unit tested with [PHPUnit](https://phpunit.de/). We aim to have all functionality covered by unit tests. When submitting patches, you are strongly encouraged to provide unit tests. Patches without tests are not accepted.

## Code Coverage

Code coverage is checked by [Coveralls](https://coveralls.io/repos/Miljar/php-exif). Respect the boy scout rule for PHP developers: Always try to keep the Code Coverage level on par or above with the current level.

## Most important Rule

All contributions are welcomed and greatly appreciated.
