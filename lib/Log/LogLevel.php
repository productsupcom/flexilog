<?php

namespace Productsup\Flexilog\Log;

/**
 * Extends the PSR LogLevel with another level of verbosity
 */
class LogLevel extends \Psr\Log\LogLevel
{
    const TRACE = 'trace';
}
