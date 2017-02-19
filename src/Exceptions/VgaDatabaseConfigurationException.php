<?php
/**
 * Created: 2017-02-19 16:10
 */

namespace VgaDatabase\Exceptions;

use \Exception;

class VgaDatabaseConfigurationException  extends Exception implements VgaException
{
    private $faultySettings;

    /**
     * VgaDatabaseConfigurationException constructor.
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->faultySettings = $settings;
        parent::__construct("Error in configuration");
    }

    public function toPrintableString(): string
    {
        $string = "<pre>VgaDatabase Configuration error:<br>";
        foreach ($this->faultySettings as $setting => $value) {
            $string .= "[ $setting => $value ]<br>";
        }
        $string .= "</pre>";

        return $string;
    }


}