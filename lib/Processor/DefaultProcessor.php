<?php

namespace Productsup\Flexilog\Processor;

/**
 * Default Processor
 */
class DefaultProcessor extends AbstractProcessor implements ProcessorInterface
{
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
                $newkey = '_'.$contextKey;
                $context[$newkey] = $context[$contextKey];
                unset($context[$contextKey]);
                $contextKey = $newkey;
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
}
