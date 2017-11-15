<?php

namespace Productsup\Flexilog\Handler;

use \Productsup\Flexilog\Processor\ProcessorInterface;

/**
 * Handler Interface for the Flexilog endpoint handlers
 */
interface HandlerInterface
{
    /**
     * Construct the Handler, optionally with a minimal logging level
     *
     * @param string  $minimalLevel the minimal severity of the LogLevel to start logging with
     * @param integer $verbose      the Verbosity of the Log
     * @param array   $additionalParameters optional additional parameters required for the Handler
     * @param \Productsup\Flexilog\Processor $processor the Processor to be used for the Handler, if none supplied
     *                              the DefaultProcessor one will be used.
     */
    public function __construct($minimalLevel = 'debug',
                                $verbose = 0,
                                array $additionalParameters = array(),
                                ProcessorInterface $processor = null);

    /**
     * Write received Log information through the Handlers mechanism
     *
     * @param \Psr\LogLevel $level
     * @param string        $message
     * @param array         $context
     */
    public function write($level, $message, array $context = array());
}
