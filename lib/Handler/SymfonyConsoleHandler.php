<?php

namespace Productsup\Flexilog\Handler;

use Productsup\Flexilog\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Write to the Symfony Console Output
 */
class SymfonyConsoleHandler extends AbstractHandler
{
    private $outputInterface = null;

    private $formatLevelMap = array(
        LogLevel::EMERGENCY => 'error',
        LogLevel::ALERT => 'error',
        LogLevel::CRITICAL => 'error',
        LogLevel::ERROR => 'error',
        LogLevel::WARNING => 'comment',
        LogLevel::DEBUG => 'info',
        LogLevel::INFO => 'info',
        LogLevel::TRACE => 'fg=gray',
    );

    // needed to test for PSR-3 compatibility
    public $logs = null;

    /**
     * {@inheritDoc}
     */
    public function __construct($minimalLevel = 'debug', $verbose = 0, $additionalParameters = array())
    {
        if (!isset($additionalParameters['outputInterface'])) {
            throw new \Exception('OutputInterface parameter must be set');
        }
        parent::__construct($minimalLevel, $verbose);
        $this->outputInterface = $additionalParameters['outputInterface'];
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

            $this->outputInterface->writeln(sprintf('<%1$s>[%2$s] %3$s</%1$s>', $this->formatLevelMap[$level], $level, $this->interpolate($shortMessageToSend, $context)));

            $i++;
        }
    }
}
