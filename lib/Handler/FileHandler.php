<?php

namespace Productsup\Flexilog\Handler;

use Productsup\Flexilog\Processor\ProcessorInterface;

/**
 * Write to a specified File
 */
class FileHandler extends AbstractHandler
{
    private $handle = null;

    /**
     * {@inheritDoc}
     *
     * @param array $additionalParameters Pass an array with the `filename` as a key/value to be used.
     */
    public function __construct($minimalLevel = 'debug',
                                $verbose = 0,
                                array $additionalParameters = array(),
                                ProcessorInterface $processor = null)
    {
        if (!isset($additionalParameters['filename'])) {
            throw new \Exception('Filename parameter must be set');
        }
        $filename = $additionalParameters['filename'];
        parent::__construct($minimalLevel, $verbose, $additionalParameters, $processor);
        if ((!file_exists($filename) && file_put_contents($filename, '') === false) ||!is_writable($filename)) {
            throw new \Exception('No write permission on file:'.$filename);
        }
        $this->handle = fopen($filename, 'a');
        if (!$this->handle) {
            throw new \Exception('Cannot open file:'.$filename);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function write($level, $message, array $context = array())
    {
        $this->writeToFile($this->processor->decorateMessage($level, $message, $context));

        if ($this->verbose >= 1) {
            $this->writeToFile($this->processor->contextSeparator());
            foreach ($context as $contextKey => $contextObject) {
                $this->writeToFile($this->processor->decorateContext($level, $contextKey, $contextObject));
            }
        }
    }

    /**
     * Writes the data to a file
     *
     * @param string $line The line to write to the file
     */
    public function writeToFile($line)
    {
        if (fwrite($this->handle, $line) === false) {
            throw new \Exception('Cannot write to file: '.$this->handle);
        }
    }
}
