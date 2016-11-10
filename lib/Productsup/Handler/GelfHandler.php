<?php

namespace Productsup\Handler;

use Gelf;

/**
 * Ouput to a Graylog server
 */
class GelfHandler extends AbstractHandler
{
    private $transport = null;
    private $publisher = null;

    public function __construct($minimalLevel = 'debug', $verbose = 0, $additionalParameters = array())
    {
        if (!isset($additionalParameters['server'])) {
            throw new \Exception('Server parameter must be set');
        }
        $port = isset($additionalParameters['port']) ? $additionalParameters['port'] : 12201;
        parent::__construct($minimalLevel, $verbose);
        $this->transport = new Gelf\Transport\UdpTransport(
            $additionalParameters['server'],
            $port,
            Gelf\Transport\UdpTransport::CHUNK_SIZE_WAN
        );
        $this->publisher = new Gelf\Publisher();
        $this->publisher->addTransport($this->transport);
    }

    public function write($level, $message, $splitFullMessage, array $context = array())
    {
        if ($message === '') {
            return;
        }

        $level = ($level == 'trace') ? 'debug' : $level;

        $i = 1;
        foreach ($splitFullMessage as $fullMessage) {
            $gelfMessage = new Gelf\Message();
            $shortMessageToSend = $message;
            if (count($splitFullMessage) != 1) {
                $shortMessageToSend = $i.'/'.count($splitFullMessage).' '.$message;
            }

            $gelfMessage->setShortMessage($shortMessageToSend)
                ->setLevel($level);

            if (isset($fullMessage)) {
                $gelfMessage->setFullMessage($fullMessage);
            }

            if (isset($context) && is_array($context)) {
                foreach ($context as $contextKey => $contextMessage) {
                    if (is_array($contextMessage)) {
                        $contextMessage = print_r($contextMessage, true);
                    }
                    $gelfMessage->setAdditional($contextKey, $contextMessage);
                }
            }

            $this->publisher->publish($gelfMessage);
            $i++;
        }
    }
}
