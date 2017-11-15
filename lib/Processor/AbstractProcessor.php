<?php

namespace Productsup\Flexilog\Processor;

/**
 * Abstract Processor to simplify the implementation of a Processor Interface
 */
abstract class AbstractProcessor implements ProcessorInterface
{
    protected $handler = null;

    /**
     * Set the Handler for the Processor
     *
     * @param \Productsup\Flexilog\Handler $handler
     *
     * @return ProcessorInterface $this
     */
    public function setHandler(\Productsup\Flexilog\Handler $handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Decorate a message before writing
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     */
    public function decorateMessage($level, $message, array $context = array())
    {
        return $message.PHP_EOL;
    }

    /**
     * Decorate the output of the context for the verbose levels
     *
     * @param string $level
     * @param string $contextKey
     * @param string $contextObject
     */
    public function decorateContext($level, $contextKey, $contextObject)
    {
        return $contextObject.PHP_EOL;
    }

    /**
     * Return a separator between the Message and the Context if applicable.
     */
    public function contextSeparator()
    {
        return PHP_EOL;
    }
}
