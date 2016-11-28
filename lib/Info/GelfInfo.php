<?php

namespace Productsup\Flexilog\Info;

class GelfInfo extends AbstractInfo
{
    protected static $requiredData = ['facility', 'host'];

    /**
     * @var string $facility the Facility that is being Logged from
     */
    public function setFacility($facility)
    {
        $this->setInternalProperty('facility', $facility);

        return $this;
    }

    /**
     * @var string $host the Host the Log originates from
     */
    public function setHost($host)
    {
        $this->setInternalProperty('host', $host);

        return $this;
    }

    public function __construct()
    {
        $this->setHost(gethostname());
    }
}
