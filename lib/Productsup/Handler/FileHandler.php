<?php

namespace Productsup\Handler;

use League;

class FileHandler extends AbstractHandler
{
    private $handle = null;

    // needed to test for PSR-3 compatibility
    public $logs = null;

    public function __construct($filename = 'log.log', $minimalLevel = 'debug', $verbose = 0)
    {
        parent::__construct($minimalLevel, $verbose);
        if (!is_writable($filename)) {
            throw new \Exception('No write permission on file:'.$filename);
        }
        $this->handle = fopen($filename, 'a');
        if (!$this->handle) {
            throw new \Exception('Cannot open file:'.$filename);
        }
    }

    public function write($level, $message, $splitFullMessage, array $context = array())
    {
        $i = 1;
        foreach ($splitFullMessage as $fullMessage) {
            $shortMessageToSend = $message;
            if (count($splitFullMessage) != 1) {
                $shortMessageToSend = $i.'/'.count($splitFullMessage).' '.$message;
            }

            $line = sprintf('%s: %s'.PHP_EOL, strtoupper($level), $message);
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

    public function writeToFile($line)
    {
        if (fwrite($this->handle, $line) === FALSE) {
            throw new \Exception('Cannot write to file: '.$this->handle);
        }
    }
}
