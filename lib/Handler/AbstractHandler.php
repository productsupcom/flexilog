<?php

namespace Productsup\Flexilog\Handler;

use Productsup\Flexilog\Processor\ProcessorInterface;

/**
 * Abstract Handler to simplify the implementation of a Handler Interface
 */
abstract class AbstractHandler implements HandlerInterface
{
    protected $logger = null;
    protected $processor = null;
    public $verbose = 0;
    public $minLevel = 7;
    const LOG_LEVELS = array(
        'emergency' => 0,
        'alert' => 1,
        'critical' => 2,
        'error' => 3,
        'warning' => 4,
        'notice' => 5,
        'info' => 6,
        'debug' => 7,
        'trace' => 8,
    );

    // needed to test for PSR-3 compatibility
    public $logs = null;

    /**
     * Construct the Handler, optionally with a minimal logging level
     *
     * @param string  $minimalLevel the minimal severity of the LogLevel to start logging with
     * @param integer $verbose      the Verbosity of the Log
     * @param array   $additionalParameters optional additional parameters required for the Handler
     * @param \Productsup\Flexilog\Processor $processor the Processor to be used for the Handler, if none supplied
     *                              the DefaultProcessor one will be used.
     */
    public function __construct($minimalLevel = 'debug',
                                $verbose = 0,
                                array $additionalParameters = array(),
                                ProcessorInterface $processor = null)
    {
        if (is_null($processor)) {
            $this->processor = new \Productsup\Flexilog\Processor\DefaultProcessor();
        } else {
            $this->processor = $processor;
        }
        $this->verbose = $verbose;
        if (array_key_exists($minimalLevel, self::LOG_LEVELS)) {
            $this->minLevel = self::LOG_LEVELS[$minimalLevel];
        }
    }

    /**
     * Initialize the Handler, is called after it's been registered with the logger
     */
    public function init()
    {
    }

    public function setProcessor(\Productsup\Flexilog\Processor $processor)
    {
        $this->processor = $processor;

        return $this;
    }

    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * Set the Logger for the Handler
     *
     * @param \Productsup\Flexilog\Logger $logger
     *
     * @return HandlerInterface $this
     */
    public function setLogger(\Productsup\Flexilog\Logger $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Prepare the Log Message before writing
     *
     * @param \Psr\LogLevel $level
     * @param string        $message Message to Log with Placeholders, defined by {curly}-braces.
     * @param array         $context Key/Value array with properties for the Placeholders.
     *
     * @return array {
     *      @var    $message
     *      @var    $context
     * }
     */
    public function prepare($level, $message, array $context = array())
    {
        $logInfo = $this->logger->getLogInfo();
        $logInfo->validate();
        $context = array_merge($context, $logInfo->getData());
        $context['loglevel'] = $level;
        $context = $this->processor->prepareContext($context);
        $message = $this->processor->interpolate($message, $context);

        return array($message, $context);
    }

    /**
     * Process the Logged message
     *
     * @param \Psr\LogLevel $level
     * @param string        $message Message to Log with Placeholders, defined by {curly}-braces.
     * @param array         $context Key/Value array with properties for the Placeholders.
     * @param boolean       $muted   Mutes the to be processed message, used when you set $verbosity
     *                               of the Handler to -1.
     *
     * @return null
     */
    public function process($level, $message, array $context = array(), $muted = false)
    {
        if ($this->verbose == -1 && $muted) {
            return;
        }
        if (self::LOG_LEVELS[$level] <= $this->minLevel) {
            list($message, $context) = $this->prepare($level, $message, $context);
            $this->logs[] = sprintf('%s %s', $level, $message);

            $this->write($level, $message, $context);
        }
    }
}
