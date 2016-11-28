<?php

namespace Productsup\Flexilog\Info;

use \Productsup\Flexilog\Exception\InfoException;

abstract class AbstractInfo implements InfoInterface
{
    protected static $requiredData = [];
    private $data = [];

    // PHP gives a Notice when setting a property that doesn't exist
    // this is usually ignored by most production servers
    // and causes long unknown debugging sessions
    public function __set($property, $value)
    {
        // if the user calls e.g. $this->setFoo() and it exists, it should call that
        // instead of going into this __set method
        if (is_callable($this, $property)) {
            return $this->${property}($value);
        }

        // if the user does $this->foo which doesn't exist as a class property
        // throw an Exception
        if (!property_exists($this, $property)) {
            throw new InfoException(sprintf('Class property `%s` does not exist', $property));
        }

        // in all other cases, set the class property.
        $this->${property} = $value;
    }

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
