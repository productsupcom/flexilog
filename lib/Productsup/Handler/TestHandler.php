<?php

namespace Productsup\Handler;

class TestHandler extends AbstractHandler
{
    public $logs = array();

    public function write($level, $message, array $context = array())
    {
        $message = $this->interpolate($message, $context);

        $this->logs[] = sprintf('%s %s', $level, $message);
    }
}
