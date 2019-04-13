<?php
declare(strict_types=1);
namespace pointybeard\Kickstarter\ExportParser;

use PHPUnit\Framework\TestCase;

use pointybeard\Kickstarter\ExportParser\Lib;
use pointybeard\Kickstarter\ExportParser\Lib\Exceptions\ZipArchiveException;
use pointybeard\Kickstarter\ExportParser\Tests\Seeders;

class BackerArchiveTest extends TestCase
{
    /**
     * Simple test to load up a valid ZIP archive into the BackerArchive object
     */
    public function testLoadValidArchiveValidCSV() : Lib\BackerArchive
    {
        $archive = new Lib\BackerArchive(__DIR__ . '/archives/valid-csv.zip');

        $this->assertTrue($archive instanceof Lib\BackerArchive);

        return $archive;
    }

    /**
     * This is a valid ZIP, but the contents are not CSV data
     */
    public function testLoadValidArchiveNonCSV() : void
    {
        $this->expectException(ZipArchiveException::class);
        $this->expectExceptionMessage('Data does not appear to be valid CSV. Please check contents.');
        (new Lib\BackerArchive(__DIR__ . '/archives/valid-noncsv.zip'))->rewards();
    }

    /**
     * This is an invalid ZIP file. It will not load.
     *
     */
    public function testLoadInvalidArchive() : void
    {
        $this->expectException(ZipArchiveException::class);
        $this->expectExceptionMessageRegExp('@Could not open file `[^`]+`. Please check it is a valid Zip archive. Error Code: \d+@');
        new Lib\BackerArchive(__DIR__ . '/archives/invalid.zip');
    }

    /**
     * Try to load a zip file that does not exist.
     */
    public function testLoadNoArchive() : void
    {
        $this->expectException(ZipArchiveException::class);
        $this->expectExceptionMessageRegExp('@Could not open file `[^`]+`. Please check it is a valid Zip archive. Error Code: \d+@');
        new Lib\BackerArchive(__DIR__ . '/archives/doesnotexist.zip');
    }

    /**
     * @depends testLoadValidArchiveValidCSV
     */
    public function testIterateOverRecords($archive) : void
    {
        foreach ($archive->rewards() as $r) {
            $this->assertTrue($r->records() instanceof Lib\RecordIterator);
        }
        $archive->close();
    }
}
