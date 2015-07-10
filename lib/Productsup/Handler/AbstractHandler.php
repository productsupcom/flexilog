<?php

namespace Productsup\Handler;

abstract class AbstractHandler implements HandlerInterface
{
    private $logger = null;
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

    public function __construct($minimalLevel = 'debug', $verbose = 0)
    {
        $this->verbose = $verbose;
        if (isset($this->logLevels[$minimalLevel])) {
            $this->minLevel = $this->logLevels[$minimalLevel];
        }
    }

    public function setLogger(\Productsup\Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Interpolates context values into the message placeholders.
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

    function prepareContext($context)
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

    public function prepare($level, $message, array $context = array())
    {
        $context = array_merge($context, get_object_vars($this->logger->logInfo));
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

    public function splitMessage($fullMessage)
    {
        $splitFullMessage = array();
        if (!is_null($fullMessage)) {
            if (is_array($fullMessage)) {
               $fullMessage = print_r($fullMessage, true);
            } else {
               $fullMessage = $fullMessage;
            }

            /* Because of the limit set by the GELF spec on the amount of chunks available
             * we have to make sure we don't send a message that exceed the amount of chunks (256)
             * times the chunk size (1420).
             * This would mean 363520bytes for a message, a whopping 355KB.
             * Some message are bigger, we split it on 220000bytes, which is a lot smaller then
             * the max size, however if we make it bigger it doesn't seem to send at all.
             * Maybe you just shouldn't try to publish a book via Gelf? ;)
             */
            $splitFullMessage = str_split($fullMessage, 220000);
        } else {
            $splitFullMessage[0] = NULL;
        }

        return $splitFullMessage;
    }

    public function process($level, $message, array $context = array())
    {
        if ($this->logLevels[$level] >= $this->minLevel) {
            list($message, $splitFullMessage, $context) = $this->prepare($level, $message, $context);
            $this->logs[] = sprintf('%s %s', $level, $message);

            $this->write($level, $message, $splitFullMessage, $context);
        }
    }
}
