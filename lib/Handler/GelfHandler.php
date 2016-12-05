<?php

namespace Productsup\Flexilog\Handler;

use Gelf;
use \Productsup\Flexilog\Exception\HandlerException;

/**
 * Ouput to a Graylog server
 */
class GelfHandler extends AbstractHandler
{
    private $transport = null;
    private $publisher = null;

    /**
     * {@inheritDoc}
     *
     * @param $additionalParameters array Pass the `server` and `port` as a key/value array
     */
    public function __construct($minimalLevel, $verbose, $additionalParameters = array())
    {
        if (!isset($additionalParameters['server'])) {
            throw new HandlerException('Server parameter must be set inside the $additionalParameters');
        }
        $port = isset($additionalParameters['port']) ? $additionalParameters['port'] : Gelf\Transport\UdpTransport::DEFAULT_PORT;
        parent::__construct($minimalLevel, $verbose);
        $this->transport = new Gelf\Transport\UdpTransport(
            $additionalParameters['server'],
            $port,
            Gelf\Transport\UdpTransport::CHUNK_SIZE_WAN
        );
        $this->publisher = new Gelf\Publisher();
        $this->publisher->addTransport($this->transport);
    }

    /**
     * {@inheritDoc}
     */
    public function write($level, $message, array $splitFullMessage, array $context = array())
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
