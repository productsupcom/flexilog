<?php

namespace Productsup\Flexilog\Handler;

/**
 * Abstract Handler to simplify the implementation of a Handler Interface
 */
abstract class AbstractHandler implements HandlerInterface
{
    protected $logger = null;
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
     */
    public function __construct($minimalLevel = 'debug', $verbose = 0)
    {
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
     * Interpolates context values into the message placeholders.
     *
     * @param string $message Message to Log with Placeholders, defined by {curly}-braces.
     * @param array  $context Key/Value array with properties for the Placeholders.
     *
     * @return string $message Message with Placeholders replaced by the context.
     */
    public function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            if (is_numeric($val)) {
                $val = (string) $val;
            }
            if (is_string($val)) {
                $replace['{'.$key.'}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /**
     * Prepare the Context before interpolation
     * Turns Objects into String representations.
     *
     * @param array $context Key/Value array with properties for the Placeholders.
     *
     * @return array $conext Cleaned context
     */
    public function prepareContext(array $context)
    {
        // cleanup any thrown exceptions
        foreach ($context as $contextKey => $contextObject) {
            // some reserved keywords
            $reserved = array('date');
            if (in_array($contextKey, $reserved)) {
                // prepend with an underscore
                $context['_'.$contextKey] = $context[$contextKey];
                unset($context[$contextKey]);
            }

            if ($contextObject instanceof \Exception) {
                $contextObject = $contextObject->__toString();
            } elseif (is_array($contextObject)) {
                $contextObject = json_encode($contextObject, true);
            } elseif ($contextObject instanceof \DateTime) {
                $contextObject = $contextObject->format(\DateTime::RFC3339);
            } elseif (is_object($contextObject)) {
                $contextObject = $contextObject->__toString();
            } elseif (is_resource($contextObject)) {
                $contextObject = get_resource_type($contextObject);
            }

            $context[$contextKey] = $contextObject;

            // clean empty values
            if (empty($contextObject) && (string) $contextObject !== '0') {
                unset($context[$contextKey]);
            }
        }

        return $context;
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
     *      @var    $splitFullMessage
     *      @var    $context
     * }
     */
    public function prepare($level, $message, array $context = array())
    {
        $logInfo = $this->logger->getLogInfo();
        $logInfo->validate();
        $context = array_merge($context, $logInfo->getData());
        $context['loglevel'] = $level;
        $context = $this->prepareContext($context);
        $message = $this->interpolate($message, $context);
        $splitFullMessage = array(null);

        if (isset($context['fullMessage'])) {
            $fullMessage = $context['fullMessage'];
            unset($context['fullMessage']);
            $fullMessage = $this->interpolate($fullMessage, $this->logger->getLogInfo()->getData());
            $fullMessage = $this->interpolate($fullMessage, $context);
            $splitFullMessage = $this->splitMessage($fullMessage);
        }

        return array($message, $splitFullMessage, $context);
    }

    /**
     * Split the Full Message into chunks before writing it to the Logger
     *
     * @param string  $fullMessage
     * @param integer $size        Defaults to 220000bytes
     *
     * @return array $splitFullMessage
     */
    public function splitMessage($fullMessage, $size = 220000)
    {
        $splitFullMessage = array(null);
        if (isset($fullMessage)) {
            if (is_array($fullMessage)) {
                $fullMessage = print_r($fullMessage, true);
            }

            /* Because of the limit set by the GELF spec on the amount of chunks available
             * we have to make sure we don't send a message that exceed the amount of chunks (256)
             * times the chunk size (1420).
             * This would mean 363520bytes for a message, a whopping 355KB.
             * Some message are bigger, we split it on 220000bytes, which is a lot smaller then
             * the max size, however if we make it bigger it doesn't seem to send at all.
             * Maybe you just shouldn't try to publish a book via Gelf? ;)
             */
            $splitFullMessage = str_split($fullMessage, $size);
        }

        return $splitFullMessage;
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
            list($message, $splitFullMessage, $context) = $this->prepare($level, $message, $context);
            $this->logs[] = sprintf('%s %s', $level, $message);

            $this->write($level, $message, $splitFullMessage, $context);
        }
    }
}
