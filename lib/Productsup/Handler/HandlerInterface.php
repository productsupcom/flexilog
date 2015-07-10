<?php

namespace Productsup\Handler;

interface HandlerInterface
{
    public function write($level, $message, $splitFullMessage, array $context = array());
}
