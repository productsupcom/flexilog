<?php

namespace Productsup\Flexilog\Handler;

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
    public function __construct($minimalLevel, $verbose, $additionalParameters = array())
    {
        if (!isset($additionalParameters['filename'])) {
            throw new \Exception('Filename parameter must be set');
        }
        $filename = $additionalParameters['filename'];
        parent::__construct($minimalLevel, $verbose);
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
        $line = sprintf('%s %s: %s'.PHP_EOL, date('H:i:s'), strtoupper($level), $message);
        $this->writeToFile($line);

        if ($this->verbose >= 1) {
            $this->writeToFile("Extra Variables:".PHP_EOL);
            foreach ($context as $contextKey => $contextObject) {
                $this->writeToFile(sprintf("\t%s: %s".PHP_EOL, $contextKey, $contextObject));
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
