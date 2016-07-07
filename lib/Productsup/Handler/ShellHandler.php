<?php

namespace Productsup\Handler;

use League;

/**
 * Write to the Shell/Bash STDERR output using multi-color
 */
class ShellHandler extends AbstractHandler
{
    private $CLImate = null;

    public function __construct($minimalLevel = 'debug', $verbose = 0)
    {
        parent::__construct($minimalLevel, $verbose);
        $this->CLImate = new League\CLImate\CLImate;
        $this->CLImate->output->defaultTo('error');
    }

    public function write($level, $message, $splitFullMessage, array $context = array())
    {
        $i = 1;
        foreach ($splitFullMessage as $fullMessage) {
            $shortMessageToSend = $message;
            if (count($splitFullMessage) != 1) {
                $shortMessageToSend = $i.'/'.count($splitFullMessage).' '.$message;
            }

            if ($this->logLevels[$level] >= 7) {
                $color = 'dark_gray';
            } elseif ($this->logLevels[$level] >= 5) {
                $color = 'green';
            } elseif ($this->logLevels[$level] == 4) {
                $color = 'yellow';
            } elseif ($this->logLevels[$level] == 3) {
                $color = 'light_red';
            } elseif ($this->logLevels[$level] <= 2) {
                $color = 'red';
            }

            $levelOut = $this->CLImate->bold();
            if ($this->logLevels[$level] <= 2) {
                $levelOut = $levelOut->blink();
            }
            $levelOut->inline(sprintf('%s <%s>%s</%s>: ', date('H:i:s'), $color, strtoupper($level), $color));
            $this->CLImate->out($shortMessageToSend);

            if ($this->verbose >= 1) {
                $this->outputVerbose($fullMessage, $context);
            }

            $i++;
        }
    }

    public function outputVerbose($fullMessage, $context)
    {
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
}
