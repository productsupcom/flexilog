<?php

namespace Productsup\Handler;

use Redis;

class RedisHandler extends AbstractHandler
{
    private $Redis = null;
    private $redisConfig = array();

    public function __construct($redisConfig = array(), $minimalLevel = 'debug', $verbose = 0)
    {
        if (!class_exists('Redis')) {
            throw new \Exception('Class Redis is not found');
        }

        if (!isset($redisConfig['host'])) {
            throw new \Exception('Redis configuration has not been provided.');
        }
        if (!isset($redisConfig['channel'])) {
            throw new \Exception('Redis Channel to Publish to has not been provided');
        }
        parent::__construct($minimalLevel, $verbose);
        $this->Redis = new Redis();
        $this->redisConfig = $redisConfig;
    }

    public function publishLine($channelName, $lineValue)
    {
        if (!$this->Redis->connect($this->redisConfig['host'], $this->redisConfig['port'])) {
            throw new \Exception('Could not connect to the Redis server.');
        }

        if (isset($this->redisConfig['password']) && !is_null($this->redisConfig['password'])) {
            $this->Redis->auth($this->redisConfig['password']);
        }

        $this->Redis->publish($channelName, $lineValue);
        $this->Redis->close();
    }

    public function write($level, $message, $splitFullMessage, array $context = array())
    {
        $line = sprintf('%s: %s'.PHP_EOL, strtoupper($level), $message);
        $this->publishLine($this->redisConfig['channel'], $line);
    }
}
