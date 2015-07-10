<?php

namespace Productsup\Handler;

class TestHandler extends AbstractHandler
{
    public $logs = array();

    public function write($level, $message, $splitFullMessage, array $context = array())
    {
        // noop;
        // taken care of in the process() in the Abstract
    }
}
