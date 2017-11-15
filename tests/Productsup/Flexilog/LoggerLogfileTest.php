<?php

use Productsup\Flexilog\Logger;
use Productsup\Flexilog\LogInfo;
use Productsup\Flexilog\Handler;

class LoggerLogfileTest extends \Psr\Log\Test\LoggerInterfaceTest
{
    private $handler = null;

    function getLogger()
    {
        $logger = new Logger(array('Test' =>
            $handler = new Handler\LogfileHandler('trace', 2, ['filename' => 'LogfileHandler_test.log']),
        ));
        $this->handler = $handler;
        return $logger;
    }

    function getLogs()
    {
        return $this->handler->logs;
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
    }
}
