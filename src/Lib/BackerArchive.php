<?php

namespace pointybeard\Kickstarter\ExportParser\Lib;
use pointybeard\Kickstarter\ExportParser\Lib\Exceptions\ZipArchiveException;

final class BackerArchive extends ZipArchiveExtended
{
    private $rewards = null;
    private $files = [];
    private $path = null;

    public function __construct($file)
    {
        $this->path = $file;
        if (($res = $this->open($this->path)) !== true) {
            throw new ZipArchiveException(
                'Could not open file `'.$this->path.'`. Please check it is a valid Zip archive. Error Code: '.$res
            );
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

    public function getArchivePath()
    {
        return $this->path;
    }

    private function process()
    {
        setlocale(LC_ALL, 'en_US.UTF8');

        foreach ($this->files as $filename) {
            if (!($fp = $this->getStream($filename))) {
                throw new ZipArchiveException("Unable to load file contents into stream. {$filename}");
            }
            $rewardName = $this->rewardNameFromFileName($filename);
            $rewardUid = $this->generateRewardUID($rewardName);

            $this->rewards[$rewardUid]['records'] = new RecordIterator($fp);
        }

        return true;
    }

    public function rewards()
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

    private function generateRewardUID($rewardName)
    {
        return md5($rewardName);
    }

    private function rewardNameFromFileName($filename)
    {
        return preg_replace_callback("/[^-]+\s+-\s+([^-]+).*/i", function ($match) {
            return trim($match[1]);
        }, $filename);
    }
}
