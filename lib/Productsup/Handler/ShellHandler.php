<?php

namespace Productsup\Handler;

use League;

class ShellHandler extends AbstractHandler
{
    protected $logInfo = null;
    private $CLImate = null;
    public $verbose = 0;
    public $minLevel = 0;

    // needed to test for PSR-3 compatibility
    public $logs = null;

    public function __construct(\Productsup\LogInfo $logInfo, $minimalLevel = 0, $verbose = 0)
    {
        $this->logInfo = $logInfo;
        $this->verbose = $verbose;
        $this->CLImate = new League\CLImate\CLImate;
        $this->CLImate->output->defaultTo('error');
        if (isset($this->logLevels[$minimalLevel])) {
            $this->minLevel = $this->logLevels[$minimalLevel];
        }
    }

    public function write($level, $message, array $context = array())
    {
        if ($this->logLevels[$level] >= $this->minLevel) {
            list($message, $splitFullMessage, $context) = $this->prepare($level, $message, $context);
            $this->logs[] = sprintf('%s %s', $level, $message);

            $i = 1;
            foreach ($splitFullMessage as $fullMessage) {
                if (count($splitFullMessage) != 1) {
                    $shortMessageToSend = $i.'/'.count($splitFullMessage).' '.$message;
                } else {
                    $shortMessageToSend = $message;
                }

                if ($this->logLevels[$level] <= 2) {
                    $color = 'green';
                } else if ($this->logLevels[$level] == 3) {
                    $color = 'yellow';
                } else if ($this->logLevels[$level] == 4) {
                    $color = 'light_red';
                } else if ($this->logLevels[$level] >= 5) {
                    $color = 'red';
                }

                if ($this->logLevels[$level] >= 5) {
                    $this->CLImate->bold()->blink()->inline(sprintf('<%s>%s</%s>: ', $color, strtoupper($level), $color));
                } else {
                    $this->CLImate->bold()->inline(sprintf('<%s>%s</%s>: ', $color, strtoupper($level), $color));
                }
                $this->CLImate->out($shortMessageToSend);

                if ($this->verbose >= 1) {
                    $color = 'cyan';
                    if (!empty($fullMessage)) {
                        $this->CLImate->inline(sprintf('<%s>%s</%s>: ', $color, 'Full Message', $color));
                        $this->CLImate->out($fullMessage);
                    }
                    if ($this->verbose >= 2) {
                        $this->CLImate->out(sprintf('<%s>%s</%s>: ', $color, 'Extra Variables', $color));
                        foreach ($context as $contextKey => $contextObject) {
                            $this->CLImate->tab()->inline(sprintf('<%s>%s</%s>: ', $color, $contextKey, $color));
                            $this->CLImate->out($contextObject);
                        }
                    }
                    if (!empty($fullMessage) || $this->verbose >= 2) {
                        $this->CLImate->br();
                    }
                }

                $i++;
            }
        }
    }
}
