<?php

namespace Productsup\Flexilog\Info;

abstract class AbstractInfo implements InfoInterface
{
    const REQUIRED_DATA = [];
    protected static $requiredData = [];

    private $data = [];

    public function setProperty($property, $value)
    {
        # so it's forbidden to overwrite a setting after inital setup?
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

    public function setRequiredData(array $data)
    {
        self::$requiredData = $data;
    }

    public static function getRequiredData()
    {
        if ($parent = get_parent_class(get_called_class())) {
            return array_merge($parent::getRequiredData(), static::REQUIRED_DATA, static::$requiredData);
        }

        return array_merge(static::REQUIRED_DATA, static::$requiredData);
    }

    public function validate()
    {
        foreach ($this->getRequiredData() as $key) {
            $this->getProperty($key);
        }
    }
}
