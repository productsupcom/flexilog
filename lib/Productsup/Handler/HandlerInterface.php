<?php

namespace Productsup\Handler;

interface HandlerInterface
{
    public function write($level, $message, array $context = array());
}
