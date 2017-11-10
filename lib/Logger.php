<?php

namespace Productsup\Flexilog;

/**
 * A PSR-3 compatible Logger that uses Handlers to output to multiple resources at the same time.
 *
 * @package Flexilog\Logger
 * @author  Productsup GmbH
 * @author  Yorick Terweijden <yt@productsup.com>
 * @license https://opensource.org/licenses/MIT MIT
 */
class Logger extends \Psr\Log\AbstractLogger
{
    private $handlers = array();
    protected $logInfo = null;
    public $autoRemove = false;

    /**
     * Initialise a new Logger with specific Handlers.
     * If no Handler is defined a default one will be initialized (Handler\GelfHandler)
     *
     * @param    array   $handlers Key/Value array where the Key is the Handler name and the object is an initialized Handler Interface
     * and the object is an initialized Handler Interface
     * @property string Handler name
     * @var      Handler\HandlerInterface Handler Interface
     * @param    LogInfo $logInfo
     */
    public function __construct(array $handlers = array(), Info\InfoInterface $logInfo = null, $autoRemove = false)
    {
        $this->logInfo = (isset($logInfo)) ? $logInfo : new Info\GenericInfo();
        $this->autoRemove = $autoRemove;

        foreach ($handlers as $handlerName => $handlerObject) {
            $handlerObject->setLogger($this);
            $handlerObject->init();
            $this->addHandler($handlerName, $handlerObject);
        }
    }

    public function setAutoRemove($autoRemove = false) {
        $this->autoRemove = (bool) $autoRemove;

        return $this;
    }

    /**
     * @param Info\InfoInterface $logInfo
     *
     * @return Logger $this
     */
    public function setLogInfo(Info\InfoInterface $logInfo)
    {
        $this->logInfo = $logInfo;

        return $this;
    }

    /**
     * Get the Info object attached to the Logger
     *
     * @return Info\InfoInterface $logInfo the Info Object
     */
    public function getLogInfo()
    {
        return $this->logInfo;
    }

    /**
     * Add a new Handler to the Logger
     *
     * @param string                   $handlerName   Handler Name
     * @param Handler\HandlerInterface $handlerObject Initialized Handler Interface
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
     * @param  string $message
     * @param  array  $context
     * @return null
     */
    public function trace($message, array $context = array())
    {
        $this->log(Log\LogLevel::TRACE, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param  mixed   $level
     * @param  string  $message
     * @param  array   $context
     * @param  boolean $muted   mutes the messages to the Handler
     *
     * @return null
     */
    public function log($level, $message, array $context = array(), $muted = false)
    {
        if (!defined('Productsup\Flexilog\Log\LogLevel::'.strtoupper($level))) {
            throw new \Psr\Log\InvalidArgumentException(sprintf('Level "%s" does not exist.', $level));
        }

        foreach ($this->handlers as $name => $handler) {
            try {
                $handler->process($level, (string) $message, $context, $muted);
            } catch (Exception\HandlerException $e) {
                if ($this->autoRemove) {
                    $this->removeHandler($name);
                } else {
                    throw new Exception\HandlerException('A Handler caused an exception, initialise Flexilog with Autoremove to unset this Handler automatically.', 0, $e);
                }
            }
        }
    }

    /**
     * Logs with an arbitrary level.
     * Convenience method, if no level is provided, Psr\Log\LogLevel::NOTICE will be used.
     *
     * @param  string  $message
     * @param  array   $context
     * @param  mixed   $level
     * @param  boolean $muted   mutes the messages to the Handler
     *
     * @return null
     */
    public function message($message, array $context = array(), $level = null, $muted = false)
    {
        if (!isset($level)) {
            $level = Log\LogLevel::NOTICE;
        } elseif (!defined('Productsup\Flexilog\Log\LogLevel::'.strtoupper($level))) {
            throw new \Psr\Log\InvalidArgumentException(sprintf('Level "%s" does not exist.', $level));
        }

        $this->log($level, $message, $context, $muted);
    }
}
