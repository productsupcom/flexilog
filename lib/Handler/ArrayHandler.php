<?php

namespace Productsup\Flexilog\Handler;

/**
 * Write to an Array
 */
class ArrayHandler extends AbstractHandler
{
    public $array = array();

    /**
     * {@inheritDoc}
     */
    public function __construct($minimalLevel = 'debug', $verbose = 0)
    {
        parent::__construct($minimalLevel, $verbose);
    }

    /**
     * {@inheritDoc}
     */
    public function write($level, $message, array $context = array())
    {
        $now = \DateTime::createFromFormat('U.u', microtime(true));
        $timestamp = $now->format('H:i:s.u');
        $arr = [
            'message' => $message,
            'level' => $level,
            'timestamp' => $timestamp,
        ];


        if ($this->verbose >= 1) {
            unset($context['loglevel']);
            $arr['variables'] = $context;
        }

        $this->array[] = $arr;
    }
}
