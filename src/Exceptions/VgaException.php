<?php
/**
 * Created: 2017-02-19 16:46
 */

namespace VgaDatabase\Exceptions;

interface VgaException
{
    /**
     * Return a printable string that represents this error.
     *
     * @return string
     */
    public function toPrintableString() : string;

}