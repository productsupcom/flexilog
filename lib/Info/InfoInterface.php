<?php

namespace Productsup\Flexilog\Info;

interface InfoInterface
{
    public function getData();
    public function setProperty($property, $value);
    public function getProperty($property);
    public function removeProperty($property);
}
