<?php
namespace MiniOrm\Driver;

use MiniOrm\Configuration;
use MiniOrm\DbalException;

/**
 * The DBAL driver for MySql.
 * 
 * Builds the DSN string to use when instantiating the PDO object for MySql connections.
 * Instantiates the data access object.
 * 
 * Contains methods that provide MySql specific features.
 *  
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class MySql implements Driver
{

    /**
     * The DSN prefix for the PDO DSN string passed to the PDO object upon instantiation.
     */
    const PREFIX = 'mysql:';

    /**
     * Holds the actual name for the database driver.
     */
    const DRIVER_NAME = 'MySql';

    /**
     * Returns the DSN string that is to be passed to the constructor when building the PDO object.
     * 
     * Method returns a PDO DSN string for MySql connections.
     * 
     * @param Configuration $oConfig
     * @return string
     * @throws DbalException
     */
    public function getConnection(Configuration $oConfig)
    {
        $aConfig = $oConfig->get();
        $sConnect = '';

        //Check that the driver is present and is the correct one.
        if (isset($aConfig['connection']) && isset($aConfig['connection']['driver']) && self::DRIVER_NAME == $aConfig['connection']['driver']) {

            //PDO will throw an exception if mysql server hostname is empty.
            $sConnect .= self::PREFIX;
            $sConnect .= 'host=';
            $sConnect .= (isset($aConfig['connection']['hostname'])) ? $aConfig['connection']['hostname'] : '';
            $sConnect .= ';dbname=';
            $sConnect .= (isset($aConfig['connection']['database'])) ? $aConfig['connection']['database'] : '';
        } else {

            throw DbalException::configurationInvalid('Cannot find the connection configurations or driver is invalid fir the MySql driver.');
        }

        return $sConnect;
    }

    /**
     * Returns an instance of the PHP data object using the mysql PDO extension.
     *
     * @param Configuration $oConfig
     * @return \PDO
     * @throws DbalException
     */
    public function getDataAccessObject(Configuration $oConfig)
    {

        $aConfig = $oConfig->get();
        $sConnect = $this->getConnection($oConfig);

        if (isset($aConfig['connection']['username']) && isset($aConfig['connection']['password'])) {

            try
            {

                return new \PDO($sConnect, $aConfig['connection']['username'], $aConfig['connection']['password']);
            } catch (\PDOException $oExp)
            {

                throw DbalException::pdoException($oExp->getMessage(), $oExp);
            }
        } else {

            throw DbalException::credentialsNotFound();
        }
    }

    /**
     * Converts a PHP date time object into a MySql date time format.
     * 
     * @param \DateTime $oDateTime
     * @return string
     */
    public function convertDateTime(\DateTime $oDateTime)
    {
        return $oDateTime->getTimestamp();
    }

    /**
     * Filters the provided string for inclustion in an SQL statement.
     * 
     * @param string $mValue
     * @return string
     */
    public function filter($mValue)
    {
        return \mysql_escape_string($mValue);
    }
}