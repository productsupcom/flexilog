<?php

namespace Productsup\Handler;

use Gelf;

class GelfHandler extends AbstractHandler
{
    private $transport = null;
    private $publisher = null;

    public function __construct($minimalLevel = 'debug', $verbose = 0)
    {
        parent::__construct($minimalLevel, $verbose);
        $this->transport = new Gelf\Transport\UdpTransport("***REMOVED***", 12201, Gelf\Transport\UdpTransport::CHUNK_SIZE_WAN);
        $this->publisher = new Gelf\Publisher();
    }

    public function write($level, $message, $splitFullMessage, array $context = array())
    {
        $i = 1;
        foreach ($splitFullMessage as $fullMessage) {
            $this->publisher->addTransport($this->transport);

            $gelfMessage = new Gelf\Message();
            if (count($splitFullMessage) != 1) {
                $shortMessageToSend = $i.'/'.count($splitFullMessage).' '.$message;
            } else {
                $shortMessageToSend = $message;
            }

            $gelfMessage->setShortMessage($shortMessageToSend)
                ->setLevel($level);

            if (!is_null($fullMessage)) {
                $gelfMessage->setFullMessage($fullMessage);
            }

            if (!is_null($context) && is_array($context)) {
                foreach ($context as $contextKey => $contextMessage) {
                    if (is_array($contextMessage)) {
                        $gelfMessage->setAdditional($contextKey, print_r($contextMessage, true));
                    } else {
                        $gelfMessage->setAdditional($contextKey, $contextMessage);
                    }
                }
            }

            $this->publisher->publish($gelfMessage);
            $i++;
        }
    }
}
