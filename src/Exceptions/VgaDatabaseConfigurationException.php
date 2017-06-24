<?php
/**
 * Created: 2017-02-19 16:10
 */

namespace VgaDatabase\Exceptions;

use VgaException\VgaException;
use VgaException\VgaExceptionType;

class VgaDatabaseConfigurationException extends VgaException
{
    protected $pathToIniFile = "";
    protected $faultySettings = [];

    /**
     * VgaDatabaseConfigurationException constructor.
     *
     * @param string $message
     * @param string $pathToIniFile
     * @param array $faultySettings
     * @param VgaExceptionType|null $previousException
     */
    public function __construct(
        string $message = "",
        string $pathToIniFile = null,
        array $faultySettings = [],
        VgaExceptionType $previousException = null)
    {
        $this->pathToIniFile = $pathToIniFile;
        $this->faultySettings = $faultySettings;
        $errorCode = 0;
        parent::__construct($message, $errorCode, $previousException);
    }

    /**
     * @return string
     */
    public function toPrintableString(): string
    {

        return sprintf(
            "VgaDatabaseConfigurationException: " . PHP_EOL
            . "%s" . PHP_EOL
            . "Settings file: %s" . PHP_EOL
            . "Current Settings: %s" . PHP_EOL,

            parent::toPrintableString(),
            $this->pathToIniFile,
            $this->settingsToString()
        );

    }

    private function settingsToString()
    {
        $string = "";
        if (!empty($this->faultySettings)) {

            foreach ($this->faultySettings as $setting => $value) {
                $string .= "[ $setting => $value ]" . PHP_EOL;
            }

        }

        return $string;
    }

}