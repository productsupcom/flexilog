<?php

namespace Productsup\Flexilog\Info;

use \Productsup\Flexilog\Exception\InfoException;

abstract class AbstractInfo implements InfoInterface
{
    private $data = [];
    protected static $requiredData = [];

    public function setProperty($property, $value, $internal = false)
    {
        if (!$internal) {
            $method = 'set'.ucfirst($property);
            if (is_callable(array($this, $method), false)) {
                $this->{$method}($value);
            }
        }
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

        throw new InfoException(sprintf('Property `%s` is not set.', $property));
    }

    public function hasProperty($property)
    {
        return isset($this->data[$property]);
    }

    public function removeProperty($property)
    {
        unset($this->data[$property]);

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setRequiredData(array $requiredData)
    {
        if (count(static::$requiredData) !== 0) {
            throw new InfoException(sprintf('Required Data is already set for class %s. Extend it if you want require more.', get_class($this)));
        }

        self::$requiredData = $requiredData;

        return $this;
    }

    public static function getRequiredData()
    {
        if ($parent = get_parent_class(get_called_class())) {
            return array_merge($parent::getrequiredData(), static::$requiredData);
        }
        return static::$requiredData;
    }

    public function validate()
    {
        foreach ($this->getrequiredData() as $key) {
            $this->getProperty($key);
        }
    }
}
