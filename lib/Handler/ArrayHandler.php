<?php

namespace Productsup\Flexilog\Handler;

/**
 * Write to an Array
 */
class ArrayHandler extends AbstractHandler
{
    public $array = array();

    public function __construct($minimalLevel = 'debug', $verbose = 0)
    {
        parent::__construct($minimalLevel, $verbose);
    }

    public function write($level, $message, $splitFullMessage, array $context = array())
    {
        $i = 1;
        foreach ($splitFullMessage as $fullMessage) {
            $shortMessageToSend = $message;
            if (count($splitFullMessage) != 1) {
                $shortMessageToSend = $i.'/'.count($splitFullMessage).' '.$message;
            }

            $now = \DateTime::createFromFormat('U.u', microtime(true));
            $timestamp = $now->format('H:i:s.u');
            $arr = [
                'message' => $shortMessageToSend,
                'level' => $level,
                'timestamp' => $timestamp
                ];


            if ($this->verbose >= 1) {
                if (!empty($fullMessage)) {
                    $arr['fullMessage'] = $fullMessage;
                }
                if ($this->verbose >= 2) {
                    unset($context['loglevel']);
                    $arr['variables'] = $context;
                }
            }

            $this->array[] = $arr;

            $i++;
        }
    }
}