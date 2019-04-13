<?php

namespace pointybeard\Kickstarter\ExportParser\Lib;

use pointybeard\Kickstarter\ExportParser\Lib\Exceptions\ZipArchiveException;

class BackerArchive extends ZipArchiveExtended
{
    private $rewards = null;
    private $path = null;

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
        return true;
    }

    public function __destruct()
    {
        if(!is_null($this->rewards)) {
            for ($ii = 0; $ii < count($this->rewards); $ii++) {
                unset($this->rewards[$ii]->records);
            }
        }
    }

    public function getArchivePath() : string
    {
        return $this->path;
    }

    private function getRewardRecordsIterator($filename)
    {
        setlocale(LC_ALL, 'en_US.UTF8');

        if (!($fp = $this->getStream($filename))) {
            throw new ZipArchiveException("Unable to load file contents into stream. {$filename}");
        }

        return new RecordIterator($fp);
    }

    public function rewards() : array
    {
        if (is_null($this->rewards)) {
            $this->rewards = [];

            // Iterate over each file to find the backer reward levels
            for ($ii = 0; $ii < $this->count(); ++$ii) {
                $this->rewards[] = new Models\Reward(
                    $this->getRewardRecordsIterator($this->getNameIndex($ii))
                );
            }
        }

        return $this->rewards;
    }

    public function & findRewardByUUID($uuid)
    {
        for ($ii = 0; $ii < count($this->rewards()); $ii++) {
            if ($this->rewards[$ii]->UUID() == $uuid) {
                return $this->rewards[$ii];
            }
        }

        return null;
    }

    public function toArray()
    {
        $result = [
            "path" => $this->path,
            "rewards" => []
        ];

        foreach ($this->rewards() as $r) {
            $result['rewards'][] = $r->toArray();
        }

        return $result;
    }

    public function toJson()
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
