<?php

use Productsup\Flexilog\Logger;
use Productsup\Flexilog\LogInfo;
use Productsup\Flexilog\Handler;

class LoggerFileTest extends \Psr\Log\Test\LoggerInterfaceTest
{
    private $handler = null;

    function getLogger()
    {
        $logger = new Logger(array('Test' =>
            $handler = new Handler\FileHandler('trace', 0, ['filename' => '/Users/twisted/productsup/logger/test.log']),
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
