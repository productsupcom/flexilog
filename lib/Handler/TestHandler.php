<?php

namespace Productsup\Flexilog\Handler;

/**
 * Output to an internal array for PSR-3 compatibility testing
 */
class TestHandler extends AbstractHandler
{
    public $logs = array();

    /**
     * {@inheritDoc}
     */
    public function write($level, $message, array $context = array())
    {
        // noop;
        // taken care of in the process() in the Abstract
    }
}
