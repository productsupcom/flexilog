<?php

namespace Productsup\Handler;

use Redis;

/**
 * Publish to a Redis channel
 */
class RedisHandler extends AbstractHandler
{
    private $Redis = null;
    private $redisConfig = array();
    private $fingersCrossed = false;

    public function __construct($redisConfig = array(), $minimalLevel = 'debug', $verbose = 0, $fingersCrossed = false)
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
        $this->fingersCrossed = $fingersCrossed;
        $this->Redis = new Redis();
        $this->redisConfig = $redisConfig;
        parent::__construct($minimalLevel, $verbose);
    }

    public function publishLine($channelName, $lineValue)
    {
        if (!$this->Redis->connect($this->redisConfig['host'], $this->redisConfig['port'])) {
            if ($this->fingersCrossed) {
                return;
            }

            throw new \Exception('Could not connect to the Redis server.');
        }

        if (isset($this->redisConfig['password'])) {
            $this->Redis->auth($this->redisConfig['password']);
        }

        $this->Redis->publish($channelName, $lineValue);
        $this->Redis->close();
    }

    public function write($level, $message, $splitFullMessage, array $context = array())
    {
        $line = sprintf('%s: %s'.PHP_EOL, strtoupper($level), $message);

        $line = array(
            'date'    => date('Y-m-d'),
            'time'    => date('H:i:s'),
            'type'    => $level,
            'message' => $message,
            'process' => getenv('PRODUCTSUP_PID'),
            'host'    => gethostname()
        );

        $lineValue = json_encode($line).PHP_EOL;
        $this->publishLine($this->redisConfig['channel'], $lineValue);
    }
}
