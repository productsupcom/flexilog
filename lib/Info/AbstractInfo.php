<?php

namespace Productsup\Flexilog\Info;

use \Productsup\Flexilog\Exception\InfoException;

/**
 * Implements an Abstract for the InfoInterface that has almost all required
 * methods preimplemented to make the actual class that extends on this
 * only require a few setters.
 */
abstract class AbstractInfo implements InfoInterface
{
    protected static $requiredData = [];
    private $data = [];

    /**
     * {@inheritDoc}
     */
    public function setProperty($property, $value)
    {
        $method = 'set'.ucfirst($property);
        if (is_callable(array($this, $method), false)) {
            $this->{$method}($value);
        }

        $this->data[$property] = $value;

        return $this;
    }
    /**
     * {@inheritDoc}
     */
    public function getProperty($property)
    {
        if (isset($this->data[$property])) {
            return $this->data[$property];
        }

        throw new InfoException(sprintf('Property `%s` is not set.', $property));
    }

    /**
     * {@inheritDoc}
     */
    public function hasProperty($property)
    {
        return isset($this->data[$property]);
    }

    /**
     * {@inheritDoc}
     */
    public function removeProperty($property)
    {
        unset($this->data[$property]);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function setRequiredData(array $data)
    {
        self::$requiredData = array_unique(array_merge(self::$requiredData, $data));

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public static function getRequiredData()
    {
        if ($parent = get_parent_class(get_called_class())) {
            return array_unique(array_merge($parent::getRequiredData(), static::$requiredData));
        }

        return static::$requiredData;
    }

    /**
     * {@inheritDoc}
     */
    public function validate()
    {
        foreach ($this->getRequiredData() as $key) {
            $this->getProperty($key);
        }

        return $this;
    }

    /**
     * Set an Internal Property bypassing the setProperty convenience method
     * for checking if a setter for the property is available.
     *
     * @param string $property keyname for the property
     * @param string $value    value for the specified key
     *
     * @return InfoInterface $this returns the object
     */
    protected function setInternalProperty($property, $value)
    {
        $this->data[$property] = $value;

        return $this;
    }
}
