<?php
namespace MiniOrm;

/**
 * Exception class for database abstraction layer components.
 * 
 * Covers drivers, configurations and data access object instantiation.
 * Future development path could include creating dedicated exception classes for configurations and 
 * drivers.
 *  
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class DbalException extends \Exception
{

    /**
     * Thrown when the database users username or password is missing from configurations.
     * 
     * @return \self
     */
    public static function credentialsNotFound()
    {
        return new self('Database connection username or password is missing in configurations.');
    }

    /**
     * Thrown if a configuration setting is missing.
     * 
     * @return \self
     */
    public static function invalidConfiguration($sMessage)
    {
        return new self('Problem encountered with ORM configuration. ' . $sMessage);
    }

    /**
     * Driver not found exception.
     * 
     * @param string $sDriver
     * @return \self
     */
    public static function driverNotFound($sDriver)
    {
        return new self('Cannot find the requested driver class.  Provided driver was ' . $sDriver);
    }

    /**
     * Thrown when the configuration file cannot be found.
     * 
     * @param string $sPath
     * @return \self
     */
    public static function configurationNotFound($sPath)
    {
        return new self('Cannot find the configuration file using the given path. File cannot be found.  Path is ' . $sPath);
    }

    /**
     * Thrown when the configuration file is not readable.
     * 
     * @param string $sPath
     * @return \self
     */
    public static function configurationNotReadable($sPath)
    {
        return new self('Cannot read the configuration file using the given path. Path is ' . $sPath);
    }

    /**
     * Thrown when the configuration file is not readable.
     * 
     * @param string $sPath
     * @return \self
     */
    public static function configurationInvalid($sPath)
    {
        return new self('Configurations are not in a valid format. Check your configurations.  Path is ' . $sPath);
    }

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
