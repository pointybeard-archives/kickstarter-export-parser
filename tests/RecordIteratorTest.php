<?php
namespace pointybeard\Kickstarter\ExportParser;
use pointybeard\Kickstarter\ExportParser\Lib\Exceptions\ZipArchiveException;
use pointybeard\Kickstarter\ExportParser\Tests\Seeders;
use pointybeard\Kickstarter\ExportParser\Lib;

class RecordIteratorTest extends \PHPUnit_Framework_TestCase
{
	private static $archive;
	
	/**
	 * This is used to generate new, valid, CSV data.
	 */
	public static function setUpBeforeClass(){
		// Create a small sample of data to iterate over.
		$archiveSeeder = new Seeders\generateValidData();
		$archiveSeeder->createValidCSVArchive(__DIR__ . '/archives/record-iterator-data.zip', 5, 0);
		self::$archive = new Lib\BackerArchive(__DIR__ . '/archives/record-iterator-data.zip');
	}
	
    public static function tearDownAfterClass()
    {
        self::$archive->close();
		unlink(__DIR__ . '/archives/record-iterator-data.zip');
    }

	public function testIterateOverRecords(){
		$it = current(self::$archive->Rewards())['records'];

		$this->assertTrue($it instanceof Lib\RecordIterator);
		
		$count = 0;
		
        do {
            $record = $it->current();
			$this->assertTrue($record instanceof Lib\Models\Record);
			
			$recordRaw = $it->current(false);
			$this->assertTrue(is_array($recordRaw));

			++$count;
            $it->next();
        } while ($it->valid());
		
		$this->assertEquals(5, $count);
		
		return $record;
	}
	
	/**
	 * @depends testIterateOverRecords
	 */
	public function testValidRecord($record){
		// Check the structure of the record now
		$this->assertArrayHasKey('basic', $record->toArray(), 'Unable to locate `basic` of record array.');
		$this->assertArrayHasKey('custom', $record->toArray(), 'Unable to locate `custom` of record array.');
		$this->assertArrayHasKey('survey', $record->toArray(), 'Unable to locate `survey` of record array.');
		
		$json = $record->toJson();
		$json_decoded = json_decode($json);

		$this->assertNotNull($json_decoded, 'Json could not be decoded.');
		
		$this->assertTrue($json_decoded->basic instanceof \StdClass, 'Unable to locate ->basic of decoded record.');
		$this->assertTrue($json_decoded->custom instanceof \StdClass, 'Unable to locate ->custom of decoded record.');
		$this->assertTrue($json_decoded->survey instanceof \StdClass, 'Unable to locate ->survey of decoded record.');
		
	}

	/**
	 * @depends testIterateOverRecords
	 * @param Lib/Models/Record $record
	 */
	public function testRecordIsSet($record){

		$this->assertTrue(isset($record->BackerName), 'Basic value should be set');
		$this->assertTrue(isset($record->RewardMinimum), 'Survey value should be set');
		$this->assertFalse(isset($record->MissingKey), 'Missing key should not should be set');
	}
}
