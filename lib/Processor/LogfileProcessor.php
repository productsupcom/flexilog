<?php

namespace Productsup\Flexilog\Processor;

/**
 * Logfile Processor
 */
class LogfileProcessor extends DefaultProcessor implements ProcessorInterface
{
    /**
     * {@inheritDoc}
     */
    public function decorateMessage($level, $message, array $context = array())
    {
        return sprintf('%s %s: %s%s', date('H:i:s'), strtoupper($level), $message, PHP_EOL);
    }

    /**
     * {@inheritDoc}
     */
    public function decorateContext($level, $contextKey, $contextObject)
    {
        return sprintf("\t%s: %s%s", $contextKey, $contextObject, PHP_EOL);
    }

    /**
     * {@inheritDoc}
     */
    public function contextSeparator()
    {
        return 'Extra variables:'.PHP_EOL;
    }
}
