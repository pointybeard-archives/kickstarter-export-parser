<?php

namespace pointybeard\Kickstarter\ExportParser\Lib;

use pointybeard\Kickstarter\ExportParser\Lib\Exceptions\ZipArchiveException;

class BackerArchive extends ZipArchiveExtended
{
    protected $rewards = null;
    protected $files = [];
    protected $path = null;

    public function __construct($file)
    {
        $this->path = $file;
        if (($res = $this->open($this->path)) !== true) {
            throw new ZipArchiveException(sprintf(
                'Could not open file `%s`. Please check it is a valid Zip archive. Error Code: %s',
                $this->path,
                $res
            ));
        }

        // Make sure the rewards array is populated
        $this->rewards();

        // This will create the reward iterators
        $this->process();

        return true;
    }

    public function __destruct()
    {
        foreach (array_keys($this->rewards) as $rewardUid) {
            unset($this->rewards[$rewardUid]['reccords']);
        }
    }

    public function getArchivePath() : string
    {
        return $this->path;
    }

    protected function process() : bool
    {
        setlocale(LC_ALL, 'en_US.UTF8');

        foreach ($this->files as $filename) {
            if (!($fp = $this->getStream($filename))) {
                throw new ZipArchiveException("Unable to load contents of {$filename} into stream.");
            }
            $rewardName = $this->rewardNameFromFileName($filename);
            $rewardUid = $this->generateRewardUID($rewardName);

            $this->rewards[$rewardUid]['records'] = new RecordIterator($fp);
        }

        return true;
    }

    public function rewards() : array
    {
        if (is_null($this->rewards)) {
            $this->rewards = [];

            // Iterate over each file to find the backer reward levels
            for ($ii = 0; $ii < $this->count(); ++$ii) {
                $filename = $this->getNameIndex($ii);
                $contents = $this->getFromIndex($ii);
                $this->files[] = $filename;
                $rewardName = $this->rewardNameFromFileName($filename);
                $this->rewards[$this->generateRewardUID($rewardName)] = ['name' => $rewardName, 'records' => null];
            }
        }

        return $this->rewards;
    }

    protected function generateRewardUID($rewardName) : string
    {
        return md5($rewardName);
    }

    protected function rewardNameFromFileName($filename) : string
    {
        // Removed the code that does the reward extraction as it is causing
        // issues when Kickstarter change their filename formatting (fixes #4)
        // https://github.com/pointybeard/kickstarter-export-parser/issues/4
        return $filename;
    }
}
