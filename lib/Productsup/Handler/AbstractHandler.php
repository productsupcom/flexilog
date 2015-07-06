<?php

namespace Productsup\Handler;

abstract class AbstractHandler implements HandlerInterface
{
    /**
     * Interpolates context values into the message placeholders.
     */
    function interpolate($message, array $context = array())
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
}
