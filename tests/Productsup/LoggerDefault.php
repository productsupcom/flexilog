<?php

namespace Productsup;

class LoggerFileTest extends \Psr\Log\Test\LoggerInterfaceTest
{
    private $handler = null;

    function getLogger()
    {
        $logInfo = new LogInfo();
        $logInfo->site = 397;
        $logInfo->process = 'somepid';

        $logger = new Logger(array(), $logInfo);
        $this->handler = $logger->getHandler($logger->getHandlerNames()[0]);
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
    }
}