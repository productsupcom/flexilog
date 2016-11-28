<?php

include __DIR__ . '/../vendor/autoload.php';

use Productsup\Flexilog\Info\GelfInfo;

class A extends GelfInfo {
	# duplicate requirement "host"
	# new requirement "foo"
	protected static $requiredData = ['foo', 'host'];
}

$info = new A;

print_r($info->getRequiredData());
