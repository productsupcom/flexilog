<?php

namespace Productsup\Flexilog\Processor;

/**
 * Processing Interface for the interpolating and processing of data/context for Handlers.
 */
interface ProcessorInterface
{
    /**
     * Interpolates context values into the message placeholders.
     *
     * @param string $message Message to Log with Placeholders, defined by {curly}-braces.
     * @param array  $context Key/Value array with properties for the Placeholders.
     *
     * @return string $message Message with Placeholders replaced by the context.
     */
    public function interpolate($message, array $context = array());

    /**
     * Prepare the Context before interpolation
     * Turns Objects into String representations.
     *
     * @param array $context Key/Value array with properties for the Placeholders.
     *
     * @return array $conext Cleaned context
     */
    public function prepareContext(array $context);
}
