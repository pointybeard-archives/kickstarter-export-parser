Kickstarter Export Parser
===========

Opens a zip file downloaded form Kickstarter and parses it for consumption by PHP

## Features

 * Easily traverse Kickstarter backer data

## Examples

```php
<?php
include __DIR__ . '/../vendor/autoload.php';
use pointybeard\Kickstarter\ExportParser\Lib;
use pointybeard\Kickstarter\ExportParser\Lib\Exceptions\ZipArchiveException;

$archive = new Lib\BackerArchive(__DIR__ . '/archives/Kickstarter Backer Report - All Rewards - Aug 18 07am.zip');

foreach($archive->rewards() as $r){
	do{
		$record = $r['records']->current();
		// do stuff with $record
		$r['records']->next();
	}while($r['records']->valid());
}
$archive->close();
```