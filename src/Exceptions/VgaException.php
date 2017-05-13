<?php
/**
 * Created: 2017-02-19 16:46
 */

namespace VgaDatabase\Exceptions;

use Exception;

abstract class VgaException extends Exception
{

    /** @var VgaException */
    protected $previousVgaException = null;

    /**
     * VgaDatabaseException constructor.
     */
    public function __construct($message = "", VgaException $previousException = null)
    {

        $this->previousVgaException = $previousException;

        parent::__construct($message);
    }

    /**
     * Return a printable string that represents this error.
     *
     * @return string
     */
    abstract public function toPrintableString(): string;

    /**
     * Return the prevoius VgaException
     *
     * @return VgaException|null
     */
    public function getPreviousInStack()
    {
        if (!empty($this->previousVgaException)) {
            return $this->previousVgaException;
        } else {
            return null;
        }
    }

    /**
     * Returns a printable string that represents all errors in stack
     *
     * @return string
     */
    public function fullStackToPrintableString(): string
    {
        $currentExeption = $this;
        $string = "";

        do {
            $string .= $currentExeption->toPrintableString();
            $currentExeption = $currentExeption->getPreviousInStack();
        } while (!empty($currentExeption));

        return $string;
    }
}