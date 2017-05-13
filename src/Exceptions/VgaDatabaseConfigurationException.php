<?php
/**
 * Created: 2017-02-19 16:10
 */

namespace VgaDatabase\Exceptions;

class VgaDatabaseConfigurationException extends VgaException
{
    protected  $faultySettings;

    /**
     * VgaDatabaseConfigurationException constructor.
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->faultySettings = $settings;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function toPrintableString(): string
    {
        $string = "<p>VgaDatabase Configuration error: {$this->getMessage()}</p>
                    <pre>";

        foreach ($this->faultySettings as $setting => $value) {
            $string .= "[ $setting => $value ]<br>";
        }

        $string .= "</pre>";

        return $string;
    }

}