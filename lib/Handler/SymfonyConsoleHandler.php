<?php

namespace Productsup\Flexilog\Handler;

use Productsup\Flexilog\Processor\ProcessorInterface;
use Productsup\Flexilog\Log\LogLevel;
use Productsup\Flexilog\Exception\HandlerException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

/**
 * Write to the Symfony Console Output
 */
class SymfonyConsoleHandler extends AbstractHandler
{
    private $outputInterface = null;
    private $verbosityLevelMap = array(
        LogLevel::EMERGENCY => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::ALERT => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::CRITICAL => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::ERROR => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::WARNING => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::NOTICE => OutputInterface::VERBOSITY_VERBOSE,
        LogLevel::INFO => OutputInterface::VERBOSITY_VERY_VERBOSE,
        LogLevel::DEBUG => OutputInterface::VERBOSITY_DEBUG,
        LogLevel::TRACE => OutputInterface::VERBOSITY_DEBUG,
    );

    private $formatLevelMap = array(
        LogLevel::EMERGENCY => 'error',
        LogLevel::ALERT => 'error',
        LogLevel::CRITICAL => 'error',
        LogLevel::ERROR => 'error',
        LogLevel::WARNING => 'info',
        LogLevel::NOTICE => 'info',
        LogLevel::INFO => 'info',
        LogLevel::DEBUG => 'info',
        LogLevel::TRACE => 'fg=gray',
    );

    // needed to test for PSR-3 compatibility
    public $logs = null;

    /**
     * {@inheritDoc}
     */
    public function __construct($minimalLevel = 'debug',
                                $verbose = 0,
                                array $additionalParameters = array(),
                                ProcessorInterface $processor = null)
    {
        if (!isset($additionalParameters['outputInterface'])) {
            throw new HandlerException('OutputInterface parameter must be set');
        }
        parent::__construct($minimalLevel, $verbose, $additionalParameters, $processor);
        $this->outputInterface = $additionalParameters['outputInterface'];
    }

    /**
     * {@inheritDoc}
     */
    public function write($level, $message, array $context = array())
    {
        if ($this->outputInterface->getVerbosity() >= $this->verbosityLevelMap[$level]) {
            $this->getOutputInterface($level)->writeln(
                sprintf(
                    '<%1$s>[%2$s] %3$s</%1$s>',
                    $this->formatLevelMap[$level],
                    $level,
                    $message
                )
            );
        }
    }

    private function getOutputInterface($level)
    {
        if ($this->formatLevelMap[$level] == 'error' && $this->outputInterface instanceof ConsoleOutputInterface) {
            return $this->outputInterface->getErrorOutput();
        }

        return $this->outputInterface;
    }
}
