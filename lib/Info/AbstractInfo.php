<?php

namespace Productsup\Flexilog\Info;

abstract class AbstractInfo implements InfoInterface
{
    private $data = [];
    protected static $RequiredData = [];

    public function setProperty($property, $value)
    {
        if (!isset($this->data[$property])) {
            $this->data[$property] = $value;
        }

        return $this;
    }

    public function getProperty($property)
    {
        if (isset($this->data[$property])) {
            return $this->data[$property];
        }

        throw new \Productsup\Flexilog\Exception\InfoException(sprintf('Property `%s` is not set.', $property));
    }

    public function removeProperty($property)
    {
        unset($this->data[$property]);
    }

    public function getData()
    {
        return $this->data;
    }

    public function setRequiredData(array $required_data)
    {
        self::$RequiredData = $required_data;
    }

    public static function getRequiredData()
    {
        if ($parent = get_parent_class(get_called_class())) {
            return array_merge($parent::getRequiredData(), static::$RequiredData);
        }
        return static::$RequiredData;
    }

    public function validate()
    {
        foreach ($this->getRequiredData() as $key) {
            $this->getProperty($key);
        }
    }
}
