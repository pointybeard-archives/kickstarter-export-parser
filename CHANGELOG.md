# CHANGELOG

## 1.0.4

* Small tweaks to BackerArchiveTest
* Added checks on totalRowsWithSurvey and totalRowsWithoutSurvey to make sure they are greater than zero.
* Added test cases for ResultIterator and Models\Record
* The current() method of ResultIterator has optional 'returnObject' flag which defaults to true. Should this be set to false, an array of data will be returned instead of an instance of Models\Record.
* Added toArray() and toJson() methods into Models\Record.

## 1.0.3

* Code cleanup
* Added archive seeder and dummy archives.
* Added unit testing for BackerArchive.
* Fixed AbstractModel class and file name.
* Added Faker to composer requirement.

## 1.0.2

* Code cleanup


## 1.0.1

* Made Record::generateRecordUID() public


## 1.0.0

* Initial release
