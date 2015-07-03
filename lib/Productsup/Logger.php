<?php

namespace Productsup;

class Logger extends \Psr\Log\AbstractLogger
{
    private $handlers = array();
    private $logInfo = null;

    public function __construct($name, array $handlers = array())
    {
        $this->logInfo = new LogInfo();
        $this->logInfo->loggerName = $name;

        if (empty($handlers)) {
            $handlers['Gelf'] = new Productsup\Handlers\GelfHandler($this->logInfo);
        }

        foreach ($handlers as $handlerName => $handlerClassName) {
            $this->addHandler($handlerName, $handlerClassName);
        }
    }

    public function getName()
    {
        return $this->logInfo->loggerName;
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
            $handler->write($level, $message, $context);
        }
    }

    public function message($message, array $context = array(), $level = null)
    {
        if (!isset($level)) {
            $level = \Psr\Log\LogLevel::NOTICE;
        } else if (!defined('\Psr\Log\LogLevel::'.strtoupper($level))) {
            throw new \Psr\Log\InvalidArgumentException(sprintf('Level "%s" does not exist.', $level));
        }

        call_user_func(__CLASS__.'::'.$level, $message, $context);
    }
}
