<?php

namespace pointybeard\Kickstarter\ExportParser\Lib\Models;

final class Record extends AbstractModel
{
    private static $currencyFields = [
        'Pledge Amount', 'Reward Minimum', 'Shipping Amount'
    ];

    private static $basicFields = [
        'Backer Number',
        'Backer UID',
        'Backer Name',
        'Email',
        'Pledge Amount',
        'Pledged At',
        'Pledged Status',
        'Notes',
        'Billing State/Province',
        'Billing Country',
    ];

    private static $commonSurveyFields = [
        'Reward Minimum',
        'Reward ID',
        'Rewards Sent?',
        'Survey Response',
        "Shipping Country",
        "Shipping Amount",
        'Shipping Name',
        'Shipping Address 1',
        'Shipping Address 2',
        'Shipping City',
        'Shipping State',
        'Shipping Postal Code',
        'Shipping Country Name',
        'Shipping Country Code',
        // Kickstarter added a new field 'Reward Title' to the backer export data (fixes #5)
        // https://github.com/pointybeard/kickstarter-export-parser/issues/5
        'Reward Title',
        // Kickstarter added fields 'Shipping Phone Number' and 'Shipping Delivery Notes'
        // to the backer export data
        'Shipping Phone Number',
        'Shipping Delivery Notes',
    ];

    public function __construct()
    {
        $this->properties = (object) [
            'basic' => [],
            'survey' => [],
            'custom' => [],
        ];
    }

    public function setField($name, $value) : self
    {
        if (!is_numeric($value) && in_array($name, self::$currencyFields)) {
            $value = self::convertRawKickstarterCurrencyString($value);
        }

        // Basic Fields
        if (in_array($name, self::$basicFields)) {
            $this->properties->basic[self::serialise($name)] = [
                'name' => $name,
                'value' => $value,
            ];

        // Survey Fields
        } elseif (in_array($name, self::$commonSurveyFields)) {
            $this->properties->survey[self::serialise($name)] = [
                'name' => $name,
                'value' => $value,
            ];

        // Non standard fields e.g. custom Q&A
        } else {
            $this->properties->custom[$this->UUID()] = [
                'question' => $name,
                'answer' => $value,
            ];
        }

        return $this;
    }

    public function __get($name)
    {
        if (!isset($this->properties->basic[$name]) && !isset($this->properties->survey[$name])) {
            throw new \Exception("Trying to get '{$name}'. No such property exists.");
        }

        // This logic exposes the fields in $commonSurveyFields
        return (
            isset($this->properties->basic[$name])
                ? $this->properties->basic[$name]['value']
                : $this->properties->survey[$name]['value']
        );
    }

    public function toArray() : array
    {
        $result = [];

        $it = new \AppendIterator;
        $it->append(new \ArrayIterator($this->properties->basic));
        $it->append(new \ArrayIterator($this->properties->survey));
        $it->append(new \ArrayIterator($this->properties->custom));

        foreach ($it as $item) {
            $result[$item['name']] = $item['value'];
        }

        return $result;
    }



    public function UUID()
    {
        return $this->properties->BackerUID;
    }

    public function __toString()
    {
        return implode(", ", array_walk($this->toArray(), function ($item, $key) {
            return sprintf("%s: %s", $key, $item);
        }));
    }

    public function toCsv($fp, $includeHeaders=false, $headers=null) : bool
    {
        $headers = $headers == null
            ? array_merge(self::$basicFields, self::$commonSurveyFields)
            : $headers
        ;

        $data = [];
        foreach ($headers as $field) {
            $seralized = self::serialise($field);
            $data[] = $this->$seralized;
        }

        // Add custom (survey) fields
        if (in_array('Survey Response', $headers)) {
            foreach ($this->properties->custom as $uid => $q) {
                $headers[] = $q['question'];
                $data[] = $q['answer'];
            }
        }

        if ($includeHeaders == true) {
            fputcsv($fp, $headers);
        }

        return fputcsv($fp, $data) === false;
    }

    public function toJson() : string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }

    public function __isset($name)
    {
        return array_key_exists($name, $this->properties->basic) ||
        array_key_exists($name, $this->properties->survey);
    }

    public static function serialise($value) : string
    {
        return preg_replace('@[^a-zA-Z0-9]@', '', $value);
    }

    private static function convertRawKickstarterCurrencyString($amount)
    {
        return (float)preg_replace("@^[^\d]+@", '', $amount);
    }

    public function hasAnsweredSurvey() : bool
    {
        return isset($this->SurveyResponse) && !empty($this->SurveyResponse);
    }

    public function hasAddress() : bool
    {
        return (
            $this->hasAnsweredSurvey()
            && isset($this->ShippingCountryCode)
            && !empty($this->ShippingCountryCode)
        );
    }

    public function getAddress() : ?object
    {
        if (!$this->hasAddress()) {
            return null;
        }

        $address = [];

        $addressFields = [
            'Shipping Name',
            'Shipping Address 1',
            'Shipping Address 2',
            'Shipping City',
            'Shipping State',
            'Shipping Postal Code',
            'Shipping Country Name',
            'Shipping Country Code',
        ];

        foreach ($addressFields as $field) {
            $key = self::serialise($field);
            $address[preg_replace('/^Shipping/', '', $key)] = $this->$key;
        }

        return (object) $address;
    }

    public function getSurveyAnswers() : array
    {
        if (!$this->hasAnsweredSurvey()) {
            return [];
        }

        return $this->properties->custom;
    }
}
