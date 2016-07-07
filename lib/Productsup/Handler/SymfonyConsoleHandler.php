<?php

namespace Productsup\Handler;

use Productsup\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;

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
        LogLevel::DEBUG => 'info',
        LogLevel::INFO => 'info',
        LogLevel::TRACE => 'fg=gray',
    );

    // needed to test for PSR-3 compatibility
    public $logs = null;

    public function __construct($minimalLevel = 'debug', $verbose = 0, OutputInterface $outputInterface)
    {
        parent::__construct($minimalLevel, $verbose);
        $this->outputInterface = $outputInterface;
    }

    public function write($level, $message, $splitFullMessage, array $context = array())
    {
        $i = 1;
        foreach ($splitFullMessage as $fullMessage) {
            $shortMessageToSend = $message;
            if (count($splitFullMessage) != 1) {
                $shortMessageToSend = $i.'/'.count($splitFullMessage).' '.$message;
            }

            if ($this->outputInterface->getVerbosity() >= $this->verbosityLevelMap[$level]) {
                $this->outputInterface->writeln(sprintf('<%1$s>[%2$s] %3$s</%1$s>', $this->formatLevelMap[$level], $level, $this->interpolate($message, $context)));
            }

            $i++;
        }
    }
}
