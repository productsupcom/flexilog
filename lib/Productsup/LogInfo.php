<?php

namespace Productsup;

/**
 * Log information that could be required during the output
 *
 */
class LogInfo
{
    /**
     * @var integer $site Site ID
     */
    public $site = null;

    /**
     * @var string $process Process ID
     */
    public $process = null;

    /**
     * @var string $facility the Facility that is being Logged from
     */
    public $facility = null;
}
