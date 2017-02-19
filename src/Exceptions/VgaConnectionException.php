<?php
/**
 * Created: 2017-02-19 21:09
 */

namespace VgaDatabase\Exceptions;

use \Exception;
use VgaDatabase\DatabaseConfig;

class VgaConnectionException extends Exception implements VgaException
{
    /** @var DatabaseConfig */
    private $configuration;

    /**
     * VgaConnectionException constructor.
     */
    public function __construct(DatabaseConfig $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Return a printable string that represents this error.
     *
     * @return string
     */
    public function toPrintableString(): string
    {
        $string  = "<pre>Error in configuration:<br>";
        $string  .= print_r($this->configuration, true);

        return $string;
    }
}