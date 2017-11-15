<?php

namespace Productsup\Flexilog\Handler;

use Productsup\Flexilog\Processor\ProcessorInterface;
use League;

/**
 * Write to the Shell/Bash STDERR output using multi-color
 */
class ShellHandler extends AbstractHandler
{
    private $CLImate = null;

    /**
     * {@inheritDoc}
     */
    public function __construct($minimalLevel = 'debug',
                                $verbose = 0,
                                array $additionalParameters = array(),
                                ProcessorInterface $processor = null)
    {
        parent::__construct($minimalLevel, $verbose, $additionalParameters, $processor);
        $this->CLImate = new League\CLImate\CLImate();
        $this->CLImate->output->defaultTo('error');
    }

    /**
     * {@inheritDoc}
     */
    public function write($level, $message, array $context = array())
    {
        $color = 'dark_gray';
        if (self::LOG_LEVELS[$level] >= 7) {
            $color = 'dark_gray';
        } elseif (self::LOG_LEVELS[$level] >= 5) {
            $color = 'green';
        } elseif (self::LOG_LEVELS[$level] == 4) {
            $color = 'yellow';
        } elseif (self::LOG_LEVELS[$level] == 3) {
            $color = 'light_red';
        } elseif (self::LOG_LEVELS[$level] <= 2) {
            $color = 'red';
        }

        $levelOut = $this->CLImate->bold();
        if (self::LOG_LEVELS[$level] <= 2) {
            $levelOut = $levelOut->blink();
        }
        $levelOut->inline(sprintf('%s <%s>%s</%s>: ', date('H:i:s'), $color, strtoupper($level), $color));
        $this->CLImate->out($message);

        if ($this->verbose >= 1) {
            $this->outputVerbose($context);
        }
    }

    /**
     * Outputs the data in a verbose manner to the Shell
     *
     * @param array  $context     the Context for the Log
     */
    public function outputVerbose($context)
    {
        $color = 'cyan';
        $this->CLImate->out(sprintf('<%s>%s</%s>: ', $color, 'Extra Variables', $color));
        foreach ($context as $contextKey => $contextObject) {
            $this->CLImate->tab()->inline(sprintf('<%s>%s</%s>: ', $color, $contextKey, $color));
            $this->CLImate->out($contextObject);
        }
    }
}
