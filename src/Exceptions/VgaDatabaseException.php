<?php
/**
 * Created: 2017-01-01 23:13
 */

namespace VgaDatabase\Exceptions;


class VgaDatabaseException extends \Exception implements VgaException {

    /**
     * Return a printable string that represents this error.
     *
     * @return string
     */
    public function toPrintableString(): string
    {
        return "";
    }
}
