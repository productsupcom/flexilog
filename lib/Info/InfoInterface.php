<?php

namespace Productsup\Flexilog\Info;

/**
 * Class Interface for the Info Class, used for providing a consistent context
 * for all Log messages
 */
interface InfoInterface
{
    /**
     * Returns the context data as an array
     */
    public function getData();

    /**
     * Set a single key/value property
     *
     * @param string $property keyname for the property
     * @param string $value    value for the specified key
     *
     * @return InfoInterface $this returns the object
     */
    public function setProperty($property, $value);

    /**
     * Get a single key/value property
     *
     * @param string $property keyname for the property
     */
    public function getProperty($property);

    /**
     * Check if a key exists as a property
     *
     * @param string $property keyname for the property
     *
     * @return boolean
     */
    public function hasProperty($property);

    /**
     * Remove a property with the specified key
     *
     * @param string $property keyname for the property
     *
     * @return InfoInterface $this returns the object
     */
    public function removeProperty($property);

    /**
     * Get an array of all the required properties that must be present for the
     * Class context to be considered valid.
     *
     * @return array $requiredData Array containing all required keys to be set
     */
    public static function getRequiredData();

    /**
     * Validate if all the required data is present
     *
     * @return InfoInterface $this returns the object
     */
    public function validate();
}
