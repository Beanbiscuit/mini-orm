<?php

namespace MiniOrm;

/**
 * Exception class for catching ORM related exceptions and errors.
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class OrmException extends \Exception
{

    /**
     * Captures exceptions raised by the PHP data object and wraps them in a DBAL exception.
     * 
     * @param string $sMessage
     * @param \Exception $oExp
     * @return \self
     */
    public static function pdoException($sMessage, $oExp)
    {

        return new self('PDOException: ' . $sMessage . ' ' . $oExp->getMessage(), 0, $oExp);
    }

}
