<?php

namespace pointybeard\Kickstarter\ExportParser\Lib\Models;

class Record extends AbstractModel
{
    private $properties;

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
        'Shipping Country',
        'Shipping Amount',
        'Shipping Name',
        'Shipping Address 1',
        'Shipping Address 2',
        'Shipping City',
        'Shipping State',
        'Shipping Postal Code',
        'Shipping Country Name',
        'Shipping Country Code',
    ];

    public function __construct()
    {
        $this->properties = (object) [
            'basic' => [],
            'survey' => [],
            'custom' => [],
        ];
    }

    public function setField($name, $value)
    {

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
            $this->properties->custom[self::generateRecordUID($name)] = [
                'question' => $name,
                'answer' => $value,
            ];
        }
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

    public function toArray(){
        return [
            'basic' => $this->properties->basic,
            'survey' => $this->properties->survey,
            'custom' => $this->properties->custom
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }

    public function generateRecordUID($recordName)
    {
        return md5($recordName);
    }

    public function __isset($name)
    {
        return array_key_exists($name, $this->properties->basic) ||
        array_key_exists($name, $this->properties->survey);
    }

    public static function serialise($value)
    {
        return preg_replace('@[^a-zA-Z0-9]@', '', $value);
    }

    public function hasAnsweredSurvey()
    {
        return isset($this->SurveyResponse) && !empty($this->SurveyResponse);
    }

    public function hasAddress()
    {
        return (
            $this->hasAnsweredSurvey()
            && isset($this->ShippingCountryCode)
            && !empty($this->ShippingCountryCode)
        );
    }

    public function getAddress()
    {
        if (!$this->hasAddress()) {
            return false;
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

    public function getSurveyAnswers()
    {
        if (!$this->hasAnsweredSurvey()) {
            return false;
        }

        return $this->properties->custom;
    }
}
