<?php

namespace pointybeard\Kickstarter\ExportParser\Lib\Models;

class Question extends AbstractModel
{
    public function __construct($original, $shortened=null, array $options=null)
    {
        $this->properties = (object)[
            'original' => $original,
            'shortened' => $shortened,
            'options' => $options,
        ];
    }

    public function addOption($value)
    {
        if (!is_array($this->properties->options)) {
            $this->properties->options = [];
        }
        $this->properties->options[] = $value;
    }

    public function options()
    {
        return $this->properties->options;
    }

    public function UUID()
    {
        return md5($this->properties->original);
    }
}
