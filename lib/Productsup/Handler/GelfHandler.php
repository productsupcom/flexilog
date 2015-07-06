<?php

namespace Productsup\Handler;

use Gelf;

class GelfHandler extends AbstractHandler
{
    private $logInfo = null;
    private $transport = null;
    private $publisher = null;

    // needed to test for PSR-3 compatibility
    public $logs = null;

    public function __construct(\Productsup\LogInfo $logInfo)
    {
        $this->logInfo = $logInfo;
        $this->transport = new Gelf\Transport\UdpTransport("***REMOVED***", 12201, Gelf\Transport\UdpTransport::CHUNK_SIZE_WAN);
        $this->publisher = new Gelf\Publisher();
    }

    public function setLogInfo(\Productsup\LogInfo $logInfo)
    {
        $this->logInfo = $logInfo;
    }

    public function write($level, $message, array $context = array())
    {
        list($message, $splitFullMessage, $context) = $this->prepare($level, $message, $context);
        ladybug_dump($level, $message, $splitFullMessage, $context);
        $this->logs[] = sprintf('%s %s', $level, $message);

        //return;

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
                        //->setAdditional('process', getenv('PRODUCTSUP_PID'))
                        //->setAdditional('site', $siteId);

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

            ladybug_dump($gelfMessage);
            $this->publisher->publish($gelfMessage);
            $i++;
        }
    }

    public function prepare($level, $message, array $context = array())
    {
        $context = array_merge($context, get_object_vars($this->logInfo));
        //$message = $this->interpolate($message, get_object_vars($this->logInfo));
        $message = $this->interpolate($message, $context);
        $fullMessage = null;

        if (isset($context['fullMessage'])) {
            $fullMessage = $context['fullMessage'];
            unset($context['fullMessage']);
            $fullMessage = $this->interpolate($fullMessage, get_object_vars($this->logInfo));
            $fullMessage = $this->interpolate($fullMessage, $context);
        }

        $context = $this->prepareContext($context);
        $splitFullMessage = $this->splitMessage($fullMessage);

        return array($message, $splitFullMessage, $context);
    }

    public function splitMessage($fullMessage)
    {
        $splitFullMessage = array();
        if (!is_null($fullMessage)) {
            if (is_array($fullMessage)) {
               $fullMessage = print_r($fullMessage, true);
            } else {
               $fullMessage = $fullMessage;
            }

            /* Because of the limit set by the GELF spec on the amount of chunks available
             * we have to make sure we don't send a message that exceed the amount of chunks (256)
             * times the chunk size (1420).
             * This would mean 363520bytes for a message, a whopping 355KB.
             * Some message are bigger, we split it on 220000bytes, which is a lot smaller then
             * the max size, however if we make it bigger it doesn't seem to send at all.
             * Maybe you just shouldn't try to publish a book via Gelf? ;)
             */
            $splitFullMessage = str_split($fullMessage, 220000);
        } else {
            $splitFullMessage[0] = NULL;
        }

        return $splitFullMessage;
    }
}
