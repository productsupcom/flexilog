<?php

use Productsup\Flexilog\Logger;
use Productsup\Flexilog\LogInfo;
use Productsup\Flexilog\Handler;

class LoggerArrayTest extends \Psr\Log\Test\LoggerInterfaceTest
{
    private $handler = null;

    function getLogger()
    {
        $logger = new Logger(array('Test' =>
            $handler = new Handler\ArrayHandler('trace', 2),
        ));
        $this->handler = $handler;
        return $logger;
    }

    function getLogs()
    {
        $logs = $this->handler->logs;
        return $logs;
    }

    function testFullMessage()
    {
        $logger = $this->getLogger();
        $context = array(
            'fullMessage' => 'Blablablabla bla blaaaa blaaaa {foo} blaa',
            'foo' => 'bar',
            'exception' => new \Exception('wut', 0, new \Exception('Previous')),
            'someArray' => array('yo, sup', 'nm nm', 'a' => array('foo', 'bar' => 'baz')),
            'date' => new \DateTime()
        );
        $logger->message('default message', $context);
        $logger->message('critical message', $context, 'critical');
        $logger->message('trace message', $context, 'trace');

        $logger->message('muted message', $context, 'alert', true);
    }
}
