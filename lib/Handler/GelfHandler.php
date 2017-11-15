<?php

namespace Productsup\Flexilog\Handler;

use Productsup\Flexilog\Processor\ProcessorInterface;
use Productsup\Flexilog\Exception\HandlerException;
use Productsup\Flexilog\Exception\HandlerConnectionException;
use Gelf;

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
    public function __construct($minimalLevel = 'debug',
                                $verbose = 0,
                                array $additionalParameters = array(),
                                ProcessorInterface $processor = null)
    {
        if (!isset($additionalParameters['server'])) {
            throw new HandlerException('Server parameter must be set inside the $additionalParameters');
        }
        $port = isset($additionalParameters['port']) ? $additionalParameters['port'] : Gelf\Transport\UdpTransport::DEFAULT_PORT;
        parent::__construct($minimalLevel, $verbose, $additionalParameters, $processor);
        $this->transport = new Gelf\Transport\UdpTransport(
            $additionalParameters['server'],
            $port,
            Gelf\Transport\UdpTransport::CHUNK_SIZE_WAN
        );
        $this->publisher = new Gelf\Publisher();
        $this->publisher->addTransport($this->transport);
    }

    /**
     * Split the Full Message into chunks before writing it to the Logger
     *
     * @param string  $fullMessage
     * @param integer $size        Defaults to 220000bytes
     *
     * @return array $splitFullMessage
     */
    public function splitMessage($fullMessage, $size = 220000)
    {
        $splitFullMessage = array(null);
        if (isset($fullMessage)) {
            if (is_array($fullMessage)) {
                $fullMessage = print_r($fullMessage, true);
            }

            /* Because of the limit set by the GELF spec on the amount of chunks available
             * we have to make sure we don't send a message that exceed the amount of chunks (256)
             * times the chunk size (1420).
             * This would mean 363520bytes for a message, a whopping 355KB.
             * Some message are bigger, we split it on 220000bytes, which is a lot smaller then
             * the max size, however if we make it bigger it doesn't seem to send at all.
             * Maybe you just shouldn't try to publish a book via Gelf? ;)
             */
            $splitFullMessage = str_split($fullMessage, $size);
        }

        return $splitFullMessage;
    }

    /**
     * {@inheritDoc}
     */
    public function write($level, $message, array $context = array())
    {
        if ($message === '') {
            return;
        }

        $splitFullMessage = array(null);

        if (isset($context['fullMessage'])) {
            $fullMessage = $context['fullMessage'];
            unset($context['fullMessage']);
            $fullMessage = $this->processor->interpolate($fullMessage, $this->logger->getLogInfo()->getData());
            $fullMessage = $this->processor->interpolate($fullMessage, $context);
            $splitFullMessage = $this->splitMessage($fullMessage);
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
                    $gelfMessage->setAdditional($contextKey, substr($contextMessage, 0, 31766));
                }
            }

            try {
                $this->publisher->publish($gelfMessage);
            } catch (\Exception $e) {
                throw new HandlerConnectionException('Could not publish to Gelf transport');
            }
            $i++;
        }
    }
}
