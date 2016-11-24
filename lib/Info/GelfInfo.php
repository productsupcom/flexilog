<?php

namespace Productsup\Flexilog\Info;

class GelfInfo extends AbstractInfo
{
    /**
     * @var string $facility the Facility that is being Logged from
     */
    public function setFacility($facility)
    {
        $this->setProperty('facility', $facility);

        return $this;
    }

    /**
     * @var string $host the Host the Log originates from
     */
    public function setHost($host)
    {
        $this->setProperty('host', $host);

        return $this;
    }

    public function __construct()
    {
        $this->setHost(gethostname());
    }
}
