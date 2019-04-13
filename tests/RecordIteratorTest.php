<?php
declare(strict_types=1);
namespace pointybeard\Kickstarter\ExportParser;

use PHPUnit\Framework\TestCase;

use pointybeard\Kickstarter\ExportParser\Lib\Exceptions\ZipArchiveException;
use pointybeard\Kickstarter\ExportParser\Tests\Seeders;
use pointybeard\Kickstarter\ExportParser\Lib;

class RecordIteratorTest extends TestCase
{
    private static $archive;

    /**
     * This is used to generate new, valid, CSV data.
     */
    public static function setUpBeforeClass() : void
    {
        // Create a small sample of data to iterate over.
        $archiveSeeder = new Seeders\generateValidData();
        $archiveSeeder->createValidCSVArchive(__DIR__ . '/archives/record-iterator-data.zip', 5, 0);
        self::$archive = new Lib\BackerArchive(__DIR__ . '/archives/record-iterator-data.zip');
    }

    public static function tearDownAfterClass() : void
    {
        self::$archive->close();
        unlink(__DIR__ . '/archives/record-iterator-data.zip');
    }

    public function testIterateOverRecords() : Lib\Models\Record
    {
        $it = current(self::$archive->Rewards())->records();

        $this->assertTrue($it instanceof Lib\RecordIterator);

        $count = 0;

        do {
            $record = $it->current();
            $this->assertTrue($record instanceof Lib\Models\Record);

            ++$count;
            $it->next();
        } while ($it->valid());

        $this->assertEquals(5, $count);

        return $record;
    }

    /**
     * @depends testIterateOverRecords
     * @param Lib/Models/Record $record
     */
    public function testRecordIsSet($record) : void
    {
        $this->assertTrue(isset($record->BackerName), 'BackerName value should be set');
        $this->assertTrue(isset($record->RewardMinimum), 'RewardMinimum value should be set');
        $this->assertFalse(isset($record->MissingKey), 'MissingKey should not should be set');
    }
}
