<?php

namespace Productsup\Handler;

use Gelf;

class GelfHandler extends AbstractHandler
{
    protected $logInfo = null;
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
}
