<?php

namespace Productsup;

/**
 * A PSR-3 compatible Logger that uses Handlers to output to multiple resources at the same time.
 */
class Logger extends \Psr\Log\AbstractLogger
{
    private $handlers = array();
    public $logInfo = null;

    /**
     * Initialise a new Logger with specific Handlers.
     * If no Handler is defined a default one will be initialized (Handler\GelfHandler)
     *
     * @param array $handlers Key/Value array where the Key is the Handler name
     * and the object is an initialized Handler Interface
     *      @property string Handler name
     *      @var Handler\HandlerInterface Handler Interface
     * @param LogInfo $logInfo
     */
    public function __construct(array $handlers = array(), LogInfo $logInfo = null)
    {
        $this->logInfo = (isset($logInfo)) ? $logInfo : new LogInfo();

        if (empty($handlers)) {
            $handlers['Gelf'] = new Handler\GelfHandler();
        }

        foreach ($handlers as $handlerName => $handlerObject) {
            $handlerObject->setLogger($this);
            $handlerObject->init();
            $this->addHandler($handlerName, $handlerObject);
        }
    }

    /**
     * @param LogInfo $logInfo
     *
     * @return Logger $this
     */
    public function setLogInfo(LogInfo $logInfo)
    {
        $this->logInfo = $logInfo;

        return $this;
    }

    /**
     * Set the Site ID for the LogInfo
     *
     * @param integer $siteId the Site ID
     *
     * @return Logger $this
     */
    public function setSiteId($siteId)
    {
        $this->logInfo->siteId = $siteId;

        return $this;
    }

    /**
     * Set the Process ID for the LogInfo
     *
     * @param string $pid the Process ID
     *
     * @return Logger $this
     */
    public function setProcessId($pid)
    {
        $this->logInfo->pid = $pid;

        return $this;
    }

    /**
     * Add a new Handler to the Logger
     *
     * @param string $handlerName Handler Name
     * @param Handler\HandlerInterface $handler Initialized Handler Interface
     *
     * @return Logger $this
     */
    public function addHandler($handlerName, Handler\HandlerInterface $handlerObject)
    {
        $handlerObject->setLogger($this);
        $handlerObject->init();
        $this->handlers[$handlerName] = $handlerObject;

        return $this;
    }

    /**
     * Get a Handler by name
     *
     * @param string $handlerName Handler Name
     *
     * @return Handler\HandlerInterface $handler Handler Interface
     */
    public function getHandler($handlerName)
    {
        if (isset($this->handlers[$handlerName])) {
            return $this->handlers[$handlerName];
        }

        return null;
    }

    /**
     * Get the list of Handlers registered by name
     *
     * @return array Handler names
     */
    public function getHandlerNames()
    {
        return array_keys($this->handlers);
    }

    /**
     * Remove a Handler from the Logger
     *
     * @param string $handlerName Handler Name
     *
     * @return Logger $this
     */
    public function removeHandler($handlerName)
    {
        unset($this->handlers[$handlerName]);

        return $this;
    }

    /**
     * Detailed trace information.
     * Outputs as DEBUG
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function trace($message, array $context = array())
    {
        $this->log(Log\LogLevel::TRACE, $message, $context);
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
        if (!defined('Productsup\Log\LogLevel::'.strtoupper($level))) {
            throw new \Psr\Log\InvalidArgumentException(sprintf('Level "%s" does not exist.', $level));
        }
        foreach ($this->handlers as $handler) {
            $handler->process($level, (string) $message, $context);
        }
    }

    /**
     * Logs with an arbitrary level.
     * Convenience method, if no level is provided, Psr\Log\LogLevel::NOTICE will be used.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function message($message, array $context = array(), $level = null)
    {
        if (!isset($level)) {
            $level = Log\LogLevel::NOTICE;
        } elseif (!defined('Productsup\Log\LogLevel::'.strtoupper($level))) {
            throw new \Psr\Log\InvalidArgumentException(sprintf('Level "%s" does not exist.', $level));
        }

        call_user_func(__CLASS__.'::'.$level, (string) $message, $context);
    }
}
