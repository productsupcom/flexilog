<?php

namespace Productsup\Flexilog\Info;

interface InfoInterface
{
    public function getData();
    public function setProperty($property, $value);
    public function getProperty($property);
    public function hasProperty($property);
    public function removeProperty($property);
    public static function getRequiredData();
    public function validate();
}
