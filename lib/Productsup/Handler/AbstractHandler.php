<?php

namespace Productsup\Handler;

/**
 * Abstract Handler to simplify the implementation of a Handler Interface
 */
abstract class AbstractHandler implements HandlerInterface
{
    protected $logger = null;
    public $verbose = 0;
    public $minLevel = 0;
    protected $logLevels = array(
        'emergency' => 7,
        'alert' => 6,
        'critical' => 5,
        'error' => 4,
        'warning' => 3,
        'notice' => 2,
        'info' => 1,
        'debug' => 0
    );

    // needed to test for PSR-3 compatibility
    public $logs = null;

    /**
     * Construct the Handler, optionally with a minimal logging level
     *
     * @param \Psr\LogLevel $minimalLevel the minimal severity of the LogLevel to start logging with
     * @param integer $verbose the Verbosity of the Log
     */
    public function __construct($minimalLevel = 'debug', $verbose = 0)
    {
        $this->verbose = $verbose;
        if (isset($this->logLevels[$minimalLevel])) {
            $this->minLevel = $this->logLevels[$minimalLevel];
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
     * @param \Productsup\Logger $logger
     *
     * @return HandlerInterface $this
     */
    public function setLogger(\Productsup\Logger $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @param string $message Message to Log with Placeholders, defined by {curly}-braces.
     * @param array $context Key/Value array with properties for the Placeholders.
     *
     * @return string $message Message with Placeholders replaced by the context.
     */
    public function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            if (is_string($val)) {
                $replace['{' . $key . '}'] = $val;
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
    function prepareContext(array $context)
    {
        // cleanup any thrown exceptions
        foreach ($context as $contextKey => $contextObject) {
            if ($contextObject instanceof \Exception) {
                $context[$contextKey] = $contextObject->__toString();
            } elseif (is_array($contextObject)) {
                $context[$contextKey] = json_encode($contextObject, true);
            } elseif ($contextObject instanceof \DateTime) {
                $context[$contextKey] = $contextObject->format(\DateTime::RFC3339);
            } elseif (is_object($contextObject)) {
                $context[$contextKey] = $contextObject->__toString();
            } elseif (is_resource($contextObject)) {
                $context[$contextKey] = get_resource_type($contextObject);
            }


            // some reserved keywords
            $reserved = array('date');
            if (in_array($contextKey, $reserved)) {
                // prepend with an underscore
                $context['_'.$contextKey] = $context[$contextKey];
                unset($context[$contextKey]);
            }

            // clean empty values
            if (empty($contextObject)) {
                unset($context[$contextKey]);
            }
        }

        return $context;
    }

    /**
     * Prepare the Log Message before writing
     *
     * @param \Psr\LogLevel $level
     * @param string $message Message to Log with Placeholders, defined by {curly}-braces.
     * @param array $context Key/Value array with properties for the Placeholders.
     *
     * @return array {
     *      @var $message
     *      @var $splitFullMessage
     *      @var $context
     * }
     */
    public function prepare($level, $message, array $context = array())
    {
        $context = array_merge($context, get_object_vars($this->logger->logInfo));
        $context['loglevel'] = $level;
        $message = $this->interpolate($message, $context);
        $fullMessage = null;

        if (isset($context['fullMessage'])) {
            $fullMessage = $context['fullMessage'];
            unset($context['fullMessage']);
            $fullMessage = $this->interpolate($fullMessage, get_object_vars($this->logger->logInfo));
            $fullMessage = $this->interpolate($fullMessage, $context);
        }

        $context = $this->prepareContext($context);
        $splitFullMessage = $this->splitMessage($fullMessage);

        return array($message, $splitFullMessage, $context);
    }

    /**
     * Split the Full Message into chunks before writing it to the Logger
     *
     * @param string $fullMessage
     * @param integer $size Defaults to 220000bytes
     *
     * @return array $splitFullMessage
     */
    public function splitMessage($fullMessage, $size = 220000)
    {
        $splitFullMessage = array(null);
        if (!is_null($fullMessage)) {
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
     * @param string $message Message to Log with Placeholders, defined by {curly}-braces.
     * @param array $context Key/Value array with properties for the Placeholders.
     *
     * @return null
     */
    public function process($level, $message, array $context = array())
    {
        if ($this->logLevels[$level] >= $this->minLevel) {
            list($message, $splitFullMessage, $context) = $this->prepare($level, $message, $context);
            $this->logs[] = sprintf('%s %s', $level, $message);

            $this->write($level, $message, $splitFullMessage, $context);
        }
    }
}
