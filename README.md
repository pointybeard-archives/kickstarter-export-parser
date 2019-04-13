# Kickstarter Export Parser

- Version: v1.0.12
- Date: April 13th 2019
- [Release notes](https://github.com/pointybeard/kickstarter-export-parser/blob/master/CHANGELOG.md)
- [GitHub repository](https://github.com/pointybeard/kickstarter-export-parser)

[![Latest Stable Version](https://poser.pugx.org/pointybeard/kickstarter-export-parser/version)](https://packagist.org/packages/pointybeard/kickstarter-export-parser) [![License](https://poser.pugx.org/pointybeard/kickstarter-export-parser/license)](https://packagist.org/packages/pointybeard/kickstarter-export-parser)

Opens a backer data zip file downloaded from Kickstarter and parses it for consumption.

## Installation

Kickstarter Export Parser is a utility library for inclusion in larger projects. Installation is best done via [Composer](http://getcomposer.org/). To install, use `composer require pointybeard/kickstarter-export-parser` or add `"pointybeard/kickstarter-export-parser": "~1.0"` to your `composer.json` file.

## Usage

Here is a basic example of how to use this library:

```php

use pointybeard\Kickstarter\ExportParser\Lib;

$archive = new Lib\BackerArchive(
    "/PATH/TO/KICKSTARTER/EXPORTED/DATA/HERE.zip"
);

foreach($archive->rewards() as $r){
    do{
        $record = $r->records()->current();

        // ... do stuff with $record ...

        $r->records()->next();
    } while($r->records()->valid());
}

$archive->close();

```
## Running the Test Suite

You can check that all code is passing by running the following command from the kickstarter-export-parser folder:

    ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/

If you want to run code coverage (e.g. `--coverage-html tests/reports/ ...`) you'll need xdebug. To install this, use the following commands:

    pecl channel-update pecl.php.net
    pecl install xdebug

## Support

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/pointybeard/kickstarter-export-parser/issues),
or better yet, fork the library and submit a pull request.

## Contributing

We encourage you to contribute to this project. Please check out the [Contributing documentation](https://github.com/pointybeard/kickstarter-export-parser/blob/master/CONTRIBUTING.md) for guidelines about how to get involved.

## License

"Kickstarter Export Parser" is released under the [MIT License](http://www.opensource.org/licenses/MIT).
