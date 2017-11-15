<?php

namespace Productsup\Flexilog\Handler;

use Productsup\Flexilog\Processor\ProcessorInterface;
use Productsup\Flexilog\Processor\LogfileProcessor;

/**
 * Write to a specified File
 */
class LogfileHandler extends FileHandler
{
    /**
     * {@inheritDoc}
     */
    public function __construct($minimalLevel = 'debug',
                                $verbose = 0,
                                array $additionalParameters = array(),
                                ProcessorInterface $processor = null)
    {
        $processor = new LogfileProcessor();
        parent::__construct($minimalLevel, $verbose, $additionalParameters, $processor);
    }
}
