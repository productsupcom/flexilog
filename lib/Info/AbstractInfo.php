<?php

namespace Productsup\Flexilog\Info;

use \Productsup\Flexilog\Exception\InfoException;

abstract class AbstractInfo implements InfoInterface
{
    protected static $requiredData = [];
    private $data = [];

    public function setProperty($property, $value)
    {
        $method = 'set'.ucfirst($property);
        if (is_callable(array($this, $method), false)) {
            $this->{$method}($value);
        }

        $this->data[$property] = $value;

        return $this;
    }

    protected function setInternalProperty($property, $value)
    {
        $this->data[$property] = $value;

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

    public function setRequiredData(array $data)
    {
        self::$requiredData = array_unique(array_merge(self::$requiredData, $data));

        return $this;
    }

    public static function getRequiredData()
    {
        if ($parent = get_parent_class(get_called_class())) {
            return array_unique(array_merge($parent::getRequiredData(), static::$requiredData));
        }

        return static::$requiredData;
    }

    public function validate()
    {
        foreach ($this->getRequiredData() as $key) {
            $this->getProperty($key);
        }

        return $this;
    }
}
