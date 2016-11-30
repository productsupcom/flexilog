<?php

namespace Productsup\Flexilog\Handler;

interface HandlerInterface
{

    /**
     * Write received Log information through the Handlers mechanism
     *
     * @param \Psr\LogLevel $level
     * @param string        $message
     * @param string        $splitFullMessage
     * @param array         $context
     */
    public function write($level, $message, $splitFullMessage, array $context = array());
}
