<?php

use Productsup\Flexilog\Logger;
use Productsup\Flexilog\LogInfo;
use Productsup\Flexilog\Handler;

class LoggerSpecTest extends \Psr\Log\Test\LoggerInterfaceTest
{
    private $handler = null;

    function getLogger()
    {
        $logger = new Logger(array('Test' => $handler = new Handler\TestHandler()));
        $this->handler = $handler;
        return $logger;
    }

    function getLogs()
    {
        $logs = $this->handler->logs;
        return $logs;
    }
}
