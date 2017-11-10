<?php

namespace Productsup\Flexilog\Handler;

/**
 * Write to a specified File
 */
class FileHandler extends AbstractHandler
{
    protected $filename = null;

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
        if ((!file_exists($filename) && file_put_contents($filename, '', FILE_APPEND | LOCK_EX) === false) || !is_writable($filename)) {
            throw new \Exception('No write permission on file:'.$filename);
        }
        $this->filename = $filename;
    }

    /**
     * {@inheritDoc}
     */
    public function write($level, $message, array $splitFullMessage, array $context = array())
    {
        $i = 1;
        foreach ($splitFullMessage as $fullMessage) {
            $shortMessageToSend = $message;
            if (count($splitFullMessage) != 1) {
                $shortMessageToSend = $i.'/'.count($splitFullMessage).' '.$message;
            }

            $line = sprintf('%s %s: %s'.PHP_EOL, date('H:i:s'), strtoupper($level), $shortMessageToSend);
            $this->writeToFile($line);

            if ($this->verbose >= 1) {
                if (!empty($fullMessage)) {
                    $this->writeToFile(sprintf("Full Message:".PHP_EOL."\t%s", $fullMessage));
                }
                if ($this->verbose >= 2) {
                    $this->writeToFile("Extra Variables:".PHP_EOL);
                    foreach ($context as $contextKey => $contextObject) {
                        $this->writeToFile(sprintf("\t%s: %s".PHP_EOL, $contextKey, $contextObject));
                    }
                }
                if (!empty($fullMessage) || $this->verbose >= 2) {
                    $this->writeToFile(PHP_EOL);
                }
            }

            $i++;
        }
    }

    /**
     * Writes the data to a file
     *
     * @param string $line The line to write to the file
     */
    public function writeToFile($line)
    {
        if (file_put_contents($this->filename, $line, FILE_APPEND | LOCK_EX) === false) {
            throw new \Exception('Cannot write to file: '.$this->handle);
        }
    }
}
