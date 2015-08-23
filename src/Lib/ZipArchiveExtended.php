<?php

namespace pointybeard\Kickstarter\ExportParser\Lib;

/**
 * ZipArchiveExtended class.
 *
 * Extends the built in ZipArchive class in PHP. Adds a
 * count method which returns the total number of files in
 * the archive.
 */
class ZipArchiveExtended extends \ZipArchive
{
    /**
     * Returns the total number of files in the archive.
     *
     * @return int
     */
    public function count()
    {
        $count = 0;
        while ($this->statIndex($count) !== false) {
            ++$count;
        }

        return $count;
    }
}
