<?php

namespace pointybeard\Kickstarter\ExportParser\Lib\Models;

use pointybeard\Kickstarter\ExportParser\Lib;

final class Reward extends AbstractModel
{
    public function __construct(Lib\RecordIterator $records)
    {
        $this->properties = (object)[
            'file' => self::findFileNameFromStream($records->stream()),
            'name' => self::fetchRewardNameFromRecords($records),
            'records' => $records,
            'questions' => self::fetchQuestionsFromHeaders($records),
            'products' => []
        ];
    }

    private static function findFileNameFromStream($stream)
    {
        $meta = stream_get_meta_data($stream);
        return pathinfo($meta['uri'], PATHINFO_FILENAME);
    }

    private static function fetchRewardNameFromRecords(Lib\RecordIterator $records)
    {

        // If Reward Title doesn't exist in the records,
        // try to determine a reward name by looking at the name of the
        // RecordIterator stream instead.
        if (!isset($records->current()->RewardTitle)) {
            return self::findFileNameFromStream($records->stream());
        }

        return $records->current()->RewardTitle;
    }

    private static function fetchQuestionsFromHeaders(Lib\RecordIterator $records)
    {
        $questions = [];

        $offset = array_search('Survey Response', $records->headers());

        if ($offset != false) {
            $shippingCodeOfset = array_search('Shipping Country Code', $records->headers());

            // If the 'Shipping Country Code' key is set, it means this has
            // an address as well. So we'll have to adjust the offset accordingly.
            if ($shippingCodeOfset !== false) {
                $offset = $shippingCodeOfset;
            }

            $offset++;

            foreach (array_slice($records->headers(), $offset) as $question) {
                $questions[] = new Question($question);
            }
        }

        return $questions;
    }

    public function & findQuestionByUUID($uuid)
    {
        for ($ii = 0; $ii < count($this->questions()); $ii++) {
            if ($this->properties->questions[$ii]->UUID() == $uuid) {
                return $this->properties->questions[$ii];
            }
        }

        return null;
    }

    public function & findQuestionByOriginalString($string)
    {
        for ($ii = 0; $ii < count($this->questions()); $ii++) {
            if ($this->properties->questions[$ii]->original == $string) {
                return $this->properties->questions[$ii];
            }
        }

        return null;
    }

    public function UUID()
    {
        return md5(self::findFileNameFromStream($this->records->stream()));
    }

    public function addQuestion(Question $questions)
    {
        $this->properties->questions[] = $questions;
    }

    public function questions()
    {
        return $this->properties->questions;
    }

    public function records()
    {
        return $this->properties->records;
    }

    public function products()
    {
        return $this->properties->products;
    }

    public function name()
    {
        return $this->properties->name;
    }

    public function addProduct($sku, $quantity=1)
    {
        return $this->properties->products[$sku] = $quantity;
    }

    public function toArray()
    {
        $result = [
            "file" => $this->file,
            "uuid" => $this->UUID(),
            "name" => $this->name(),
            "questions" => [],
            "products" => $this->products()
        ];

        foreach ($this->questions() as $q) {
            $result['questions'][] = $q->toArray();
        }

        return $result;
    }
}
