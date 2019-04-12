<?php
namespace pointybeard\Kickstarter\ExportParser;

use pointybeard\Kickstarter\ExportParser\Lib;
use pointybeard\Kickstarter\ExportParser\Lib\Exceptions\ZipArchiveException;
use pointybeard\Kickstarter\ExportParser\Tests\Seeders;

class BackerArchiveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Simple test to load up a valid ZIP archive into the BackerArchive object
     */
    public function testLoadValidArchiveValidCSV()
    {
        $archive = new Lib\BackerArchive(__DIR__.'/archives/valid-csv.zip');

        $this->assertTrue($archive instanceof Lib\BackerArchive);

        return $archive;
    }

    /**
     * This is a valid ZIP, but the contents are no CSV data
     *
     * @expectedException        pointybeard\Kickstarter\ExportParser\Lib\Exceptions\ZipArchiveException
     * @expectedExceptionMessage Data does not appear to be valid CSV. Please check contents.
     */
    public function testLoadValidArchiveNonCSV()
    {
        $archive = new Lib\BackerArchive(__DIR__.'/archives/valid-noncsv.zip');
    }

    /**
     * This is an invalid ZIP file. It will not load.
     *
     * @expectedException        pointybeard\Kickstarter\ExportParser\Lib\Exceptions\ZipArchiveException
     * @expectedExceptionMessageRegExp #Could not open file `[^`]+`. Please check it is a valid Zip archive. Error Code: 19#
     */
    public function testLoadInvalidArchive()
    {
        $archive = new Lib\BackerArchive(__DIR__.'/archives/invalid.zip');
    }

    /**
     * Try to load a zip file that does not exist.
     *
     * @expectedException        pointybeard\Kickstarter\ExportParser\Lib\Exceptions\ZipArchiveException
     * @expectedExceptionMessageRegExp #Could not open file `[^`]+`. Please check it is a valid Zip archive. Error Code: 11#
     */
    public function testLoadNoArchive()
    {
        $archive = new Lib\BackerArchive(__DIR__.'/archives/doesnotexist.zip');
    }

    /**
     * @depends testLoadValidArchiveValidCSV
     */
    public function testIterateOverRecords($archive)
    {
        foreach ($archive->rewards() as $r) {
            $this->assertTrue($r['records'] instanceof Lib\RecordIterator);
        }
        $archive->close();
    }
}
