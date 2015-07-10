<?php

namespace Productsup;

class Logger extends \Psr\Log\AbstractLogger
{
    private $handlers = array();
    public $logInfo = null;

    public function __construct(array $handlers = array(), LogInfo $logInfo = null)
    {
        $this->logInfo = (!is_null($logInfo)) ? $logInfo : new LogInfo();

        if (empty($handlers)) {
            $handlers['Gelf'] = new Productsup\Handlers\GelfHandler();
        }

        foreach ($handlers as $handlerName => $handlerObject) {
            $handlerObject->setLogger($this);
            $this->addHandler($handlerName, $handlerObject);
        }
    }

    public function setLogInfo(LogInfo $logInfo)
    {
        $this->logInfo = $logInfo;

        return $this;
    }

    public function setSiteId($siteId)
    {
        $this->logInfo->siteId = $siteId;

        return $this;
    }

    public function setProcessId($pid)
    {
        $this->logInfo->pid = $pid;

        return $this;
    }

    public function addHandler($handlerName, Handler\HandlerInterface $handler)
    {
        $this->handlers[$handlerName] = $handler;

        return $this;
    }

    public function getHandler($handlerName)
    {
        if (isset($this->handlers[$handlerName])) {
            return $this->handlers[$handlerName];
        }

        return null;
    }

    public function removeHandler($handlerName)
    {
        unset($this->handlers[$handlerName]);

        return $this;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        if (!defined('\Psr\Log\LogLevel::'.strtoupper($level))) {
            throw new \Psr\Log\InvalidArgumentException(sprintf('Level "%s" does not exist.', $level));
        }
        foreach ($this->handlers as $key => $handler) {
            $handler->process($level, $message, $context);
        }
    }

    public function message($message, array $context = array(), $level = null)
    {
        if (!isset($level)) {
            $level = \Psr\Log\LogLevel::NOTICE;
        } elseif (!defined('\Psr\Log\LogLevel::'.strtoupper($level))) {
            throw new \Psr\Log\InvalidArgumentException(sprintf('Level "%s" does not exist.', $level));
        }

        call_user_func(__CLASS__.'::'.$level, $message, $context);
    }
}
