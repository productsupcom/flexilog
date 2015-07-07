<?php

namespace Productsup\Handler;

abstract class AbstractHandler implements HandlerInterface
{
    protected $logInfo = null;

    public function setLogInfo(\Productsup\LogInfo $logInfo)
    {
        $this->logInfo = $logInfo;
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
            if (is_a($contextObject, 'Exception')) {
                $context[$contextKey] = $contextObject->__toString();
            } else if (is_array($contextObject)) {
                $context[$contextKey] = json_encode($contextObject, true);
            } else if (is_a($contextObject, 'DateTime')) {
                $context[$contextKey] = $contextObject->format(\DateTime::RFC3339);
            } else if (is_object($contextObject)) {
                $context[$contextKey] = $contextObject->__toString();
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
        $context = array_merge($context, get_object_vars($this->logInfo));
        //$message = $this->interpolate($message, get_object_vars($this->logInfo));
        $message = $this->interpolate($message, $context);
        $fullMessage = null;

        if (isset($context['fullMessage'])) {
            $fullMessage = $context['fullMessage'];
            unset($context['fullMessage']);
            $fullMessage = $this->interpolate($fullMessage, get_object_vars($this->logInfo));
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
}
