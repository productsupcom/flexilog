<?php

namespace Productsup\Handler;

class GelfHandler extends AbstractHandler
{
    private $logInfo = null;

    public function __construct(\Productsup\LogInfo $logInfo)
    {
        $this->logInfo = $logInfo;
    }

    public function write($level, $message, array $context = array())
    {
        ladybug_dump($level, $message, $context);
        ladybug_dump($this->logInfo);

        $message = $this->interpolate($message, get_object_vars($this->logInfo));
        $message = $this->interpolate($message, $context);
        ladybug_dump($level, $message, $context);

        return;

        //if (!is_null($fullMessage)) {
        //    if (is_array($fullMessage)) {
        //       $fullMessage = print_r($fullMessage, true);
        //    } else {
        //       $fullMessage = $fullMessage;
        //    }

        //    /* Because of the limit set by the GELF spec on the amount of chunks available
        //     * we have to make sure we don't send a message that exceed the amount of chunks (256)
        //     * times the chunk size (1420).
        //     * This would mean 363520bytes for a message, a whopping 355KB.
        //     * Some message are bigger, we split it on 220000butes, which is a lot smaller then
        //     * the max size, however if we make it bigger it doesn't seem to send at all.
        //     * Maybe you just shouldn't try to publish a book via Gelf? ;)
        //     */
        //    $splitFullMessage = str_split($fullMessage, 220000);
        //} else {
        //    $splitFullMessage[0] = NULL;
        //}

        //$transport = new Gelf\Transport\UdpTransport("***REMOVED***", 12201, Gelf\Transport\UdpTransport::CHUNK_SIZE_WAN);
        //$publisher = new Gelf\Publisher();

        //$i = 1;
        //foreach ($splitFullMessage as $fullMessage) {
        //    $publisher->addTransport($transport);

        //    $message = new Gelf\Message();
        //    if (count($splitFullMessage) != 1) {
        //        $shortMessageToSend = $i.'/'.count($splitFullMessage).' '.$shortMessage;
        //    } else {
        //        $shortMessageToSend = $shortMessage;
        //    }

        //    $message->setShortMessage($shortMessageToSend)
        //            ->setLevel($level)
        //            ->setAdditional('process', getenv('PRODUCTSUP_PID'))
        //            ->setAdditional('site', $siteId);

        //    if (!is_null($fullMessage)) {
        //        $message->setFullMessage($fullMessage);
        //    }

        //    if (!is_null($facility)) {
        //        $message->setFacility($facility);
        //    }

        //    if (!is_null($additional) && is_array($additional)) {
        //        foreach ($additional as $additionalKey => $additionalMessage) {
        //            if (is_array($additionalMessage)) {
        //                $message->setAdditional($additionalKey, print_r($additionalMessage, true));
        //            } else {
        //                $message->setAdditional($additionalKey, $additionalMessage);
        //            }
        //        }
        //    }

        //    $publisher->publish($message);
        //    $i++;
        //}
    }
}
