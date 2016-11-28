<?php

include __DIR__ . '/../vendor/autoload.php';

$info = new Productsup\Flexilog\Info\GelfInfo;

print_r($info->getRequiredData());
