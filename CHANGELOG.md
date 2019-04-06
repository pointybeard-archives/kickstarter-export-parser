# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [1.0.11]
#### Added
- Added utility functions. Included in composer autoloader
- Added `bin/stats` for producting a JSON summary of all Kickstarter export archives in a specified directory
- Added return types to methods.
- Added `__toString()` and `toCsv()` methods.

#### Changed
- Requiring packages `pointybeard/property-bag` and `pointybeard/php-cli-lib`

## [1.0.10]
#### Changed
- Now requires PHP 7.2 or later
- Added return types to methods.
- Updated unit tests for phpunit v8.
- Updated `composer.json`. Changed author information, requiring PHP7.3 or greater, using dev-master of `fzaninotto/faker` and v8.x of phpunit, and removed unnecessary 'classmap' in autoload.

## [1.0.9]
#### Fixed
- `BackerArchive::rewardNameFromFileName()` now returns the full filename instead of trying to parse it out. Still keeping as abstracted method for now. (Fixes #4)
- Added 'Reward Title' to list of fields (Fixes #5)

## [1.0.8]
#### Changed
- Record class is no longer accessing the properties array directly in `hasAnsweredSurvey()`, `hasAddress()`, and `getAddress()`

## [1.0.7]
#### Changed
- Updated for new field 'Reward ID'

## [1.0.6]
#### Fixed
- Fix `__isset` implementation (thetamind)

## [1.0.5]
#### Fixed
- Fixed bug when preparing data in `Models\Record::getAddress()` method. 'ShippingState' was being cut down to just 'tate'

## [1.0.4]
#### Added
- Added checks on `totalRowsWithSurvey()` and `totalRowsWithoutSurvey()` to make sure they are greater than zero.
- Added test cases for `ResultIterator` and `Models\Record`
- The `current()` method of `ResultIterator` has optional `returnObject` flag which defaults to true. Should this be set to false, an array of data will be returned instead of an instance of `Models\Record`.
- Added `toArray()` and `toJson()` methods into `Models\Record`.

#### Changed
- Small tweaks to `BackerArchiveTest`

## [1.0.3]
#### Added
- Added archive seeder and dummy archives.
- Added unit testing for `BackerArchive`.
- Added `Faker` to composer requirement.
-
#### Fixed
- Fixed `AbstractModel` class and file name.

## [1.0.2]
#### Changed
- Code cleanup & refactoring

## [1.0.1]
#### Changed
- Made `Record::generateRecordUID()` public

## 1.0.0
#### Added
- Initial release

[1.0.11]: https://github.com/pointybeard/symphony-classmapper/compare/1.0.10...1.0.11
[1.0.10]: https://github.com/pointybeard/symphony-classmapper/compare/1.0.9...1.0.10
[1.0.9]: https://github.com/pointybeard/symphony-classmapper/compare/1.0.8...1.0.9
[1.0.8]: https://github.com/pointybeard/symphony-classmapper/compare/1.0.7...1.0.8
[1.0.7]: https://github.com/pointybeard/symphony-classmapper/compare/1.0.6...1.0.7
[1.0.6]: https://github.com/pointybeard/symphony-classmapper/compare/1.0.5...1.0.6
[1.0.5]: https://github.com/pointybeard/symphony-classmapper/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/pointybeard/symphony-classmapper/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/pointybeard/symphony-classmapper/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/pointybeard/symphony-classmapper/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/pointybeard/symphony-classmapper/compare/1.0.0...1.0.1
