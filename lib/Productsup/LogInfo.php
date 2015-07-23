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

    /**
     * @param string $string a string like site:123;process:abcdef;facility:destination
     * @return static
     */
    public static function fromString($string) {
        $parts = explode(';',$string);
        $info = new static;
        foreach($parts as $part) {

            $property = explode(':',$part,2);
            if(property_exists($info,$property[0])) {
                $info->{$property[0]} = $property[1];
            }
        }
        return $info;
    }
}
