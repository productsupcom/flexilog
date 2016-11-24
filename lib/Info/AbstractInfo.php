<?php

namespace Productsup\Flexilog\Info;

abstract class AbstractInfo implements InfoInterface
{
    private $data = array();

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

        throw new \Exception(sprintf('Property %s does not exist'));
    }

    public function removeProperty($property)
    {
        unset($this->data[$property]);
    }

    public function getData()
    {
        return $this->data;
    }
}
