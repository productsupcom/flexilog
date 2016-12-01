<?php

namespace Productsup\Flexilog\Info;

/**
 * Info class for the Gelf format for the GelfHandler
 */
class GelfInfo extends AbstractInfo
{
    protected static $requiredData = ['host'];

    /**
     * {@inheritDoc}
     *
     * @param string $facility the Facility that is being Logged from
     */
    public function setFacility($facility)
    {
        $this->setInternalProperty('facility', $facility);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @var string $host the Host the Log originates from
     */
    public function setHost($host)
    {
        $this->setInternalProperty('host', $host);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        $this->setHost(gethostname());
    }
}
