<?php

namespace Productsup\Flexilog\Handler;

use Redis;

/**
 * Publish to a Redis channel
 */
class RedisHandler extends AbstractHandler
{
    protected $redis = null;
    protected $redisConfig = array();
    protected $fingersCrossed = false;

    /**
     * {@inheritDoc}
     */
    public function __construct($minimalLevel, $verbose, $additionalParameters = array())
    {
        if (!class_exists('Redis')) {
            throw new \Exception('Class Redis is not found');
        }
        if (!isset($additionalParameters['redisConfig'])) {
            throw new \Exception('Redis configuration has not been provided.');
        }
        $redisConfig = $additionalParameters['redisConfig'];
        if (!isset($redisConfig['channel'])) {
            throw new \Exception('Redis Channel to Publish to has not been provided');
        }
        if (isset($additionalParameters['fingersCrossed'])) {
            $this->fingersCrossed = $additionalParameters['fingersCrossed'];
        }
        $this->redis = new Redis();
        $this->redisConfig = $redisConfig;
        parent::__construct($minimalLevel, $verbose);
    }

    /**
     * Publishes the log to Redis
     *
     * @param string $channelName the Redis channelname to publish to
     * @param string $lineValue   the log message to publish
     */
    public function publishLine($channelName, $lineValue)
    {
        if (!$this->redis->connect($this->redisConfig['host'], $this->redisConfig['port'])) {
            if ($this->fingersCrossed) {
                return;
            }

            throw new \Exception('Could not connect to the Redis server.');
        }

        if (isset($this->redisConfig['password'])) {
            $this->redis->auth($this->redisConfig['password']);
        }

        $this->redis->publish($channelName, $lineValue);
        $this->redis->close();
    }

    /**
     * {@inheritDoc}
     */
    public function write($level, $message, array $splitFullMessage, array $context = array())
    {
        $line = array(
            'date'    => date('Y-m-d'),
            'time'    => date('H:i:s'),
            'type'    => $level,
            'message' => $message,
            'host'    => gethostname(),
        );

        $lineValue = json_encode($line).PHP_EOL;
        $this->publishLine($this->redisConfig['channel'], $lineValue);
    }
}
