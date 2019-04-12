<?php

namespace pointybeard\Kickstarter\ExportParser\Lib\Models;

abstract class AbstractModel
{
    protected $properties;

    abstract public function UUID();

    protected static function serialiseValue($value)
    {
        return preg_replace('@[^a-zA-Z0-9]@', '', $value);
    }

    public function __set($name, $value)
    {
        if (!property_exists($this->properties, $name)) {
            throw new \Exception("No such property {$name} exists for model.");
        }

        return $this->properties->{$name} = $value;
    }

    public function __get($name)
    {
        if (!property_exists($this->properties, $name)) {
            throw new \Exception("No such property {$name} exists for model.");
        }

        return $this->properties->{$name};
    }

    public function toArray(){
        return array_merge(
            ["uuid" => $this->UUID()], (array)$this->properties
        );
    }

    public function toJson()
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
