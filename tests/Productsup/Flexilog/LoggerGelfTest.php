<?php

use Productsup\Flexilog\Logger;
use Productsup\Flexilog\Info;
use Productsup\Flexilog\Handler;

class LoggerGelfTest extends \Psr\Log\Test\LoggerInterfaceTest
{
    private $handler = null;

    function getLogger()
    {
        $logInfo = new Info\GelfInfo();
        $logInfo->setFacility('test');
        $logger = new Logger(
            array('Test' =>
                $handler = new Handler\GelfHandler('debug', 0, ['server'=>'127.0.0.1'])
            )
        );
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
        $logger->message('fullmessage and foo and exception plus array AND date rfc', $context);
        $logger->message('trace message', $context, 'trace');
    }
}
