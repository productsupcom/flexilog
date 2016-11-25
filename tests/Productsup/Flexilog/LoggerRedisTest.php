<?php

use Productsup\Flexilog\Logger;
use Productsup\Flexilog\LogInfo;
use Productsup\Flexilog\Handler;

class LoggerRedisTest extends \Psr\Log\Test\LoggerInterfaceTest
{
    private $handler = null;

    function getLogger()
    {
        $redisConfig = array(
            'host' => '127.0.0.1',
            'port' => '6379',
            'password' => null,
            'channel' => sprintf(
                '%s_%s.log',
                '397',
                substr(md5("397a"), 0, 15).substr(md5("397b"), 5, 5)
            )
        );

        $logger = new Logger(array('Redis' =>
            $handler = new Handler\RedisHandler('debug', 0, ['redisConfig'=>$redisConfig]),
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
    }
}
