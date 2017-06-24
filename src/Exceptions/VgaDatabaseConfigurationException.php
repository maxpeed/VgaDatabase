<?php
/**
 * Created: 2017-02-19 16:10
 */

namespace VgaDatabase\Exceptions;

class VgaDatabaseConfigurationException extends VgaException
{
    protected $pathToIniFile;
    protected $faultySettings;

    /**
     * VgaDatabaseConfigurationException constructor.
     *
     * @param string $pathToIniFile
     * @param array $faultySettings
     */
    public function __construct(
        string $message,
        string $pathToIniFile = null,
        array $faultySettings = [],
        VgaException $previousException = null)
    {
        $this->pathToIniFile = $pathToIniFile;
        $this->faultySettings = $faultySettings;
        parent::__construct($message, $previousException);
    }

    /**
     * @return string
     */
    public function toPrintableString(): string
    {
        $string = "
            <p>VgaDatabase Configuration error: {$this->message}</p>
            <p>Path to INI file: {$this->pathToIniFile}</p>";

        if (!empty($this->faultySettings)) {
            $string .= "<pre>";

            foreach ($this->faultySettings as $setting => $value) {
                $string .= "[ $setting => $value ]<br>";
            }

            $string .= "</pre>";
        }

        return $string;
    }

}