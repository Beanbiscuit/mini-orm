<?php
namespace MiniOrm\Driver;

use MiniOrm\Configuration;
use MiniOrm\DbalException;

/**
 * The DBAL driver for the various versions of Sqlite.
 * 
 * Builds the DSN string to use when instantiating the PDO object for Sqlite connections.
 * 
 * Instantiates the data access object.
 * Contains methods that provide Sqlite specific features.
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class Sqlite implements Driver
{

    /**
     * Holds the default DSN prefix for sqlite connections.
     */
    const PREFIX = 'sqlite:';

    /**
     * Holds the actual name for the database driver.
     */
    const DRIVER_NAME = 'Sqlite';

    /**
     * Returns the connection string that is to be passed when building the PDO object.
     * 
     * Method returns a PDO connection string for SQLite3.
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

            //Sqlite DSN has a variety of prefixes.
            //If one is not set in configurations then assign the default held in the PREFIX constant.
            $sPrefix = null;
            if (isset($aConfig['connection']['prefix']) && !empty($aConfig['connection']['prefix'])) {

                $sPrefix = $aConfig['connection']['prefix'];
            } else {

                $sPrefix = self::PREFIX;
            }

            $sConnect .= $sPrefix;
            $sConnect .= (isset($aConfig['connection']['database'])) ? $aConfig['connection']['database'] : '';
        } else {

            throw DbalException::configurationInvalid('Cannot find the connection configurations or driver is invalid for the Sqlite driver.');
        }

        return $sConnect;
    }

    /**
     * Converts a PHP date time object into a Sqlite date time format.
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
     * Filters the string for the sqlite platform.
     * 
     * @param string $mValue
     * @return string
     */
    public function filter($mValue)
    {

        return \SQLite3::escapeString($mValue);
    }

    /**
     * Returns an instance of the PHP data object using the sqlite PDO extension.
     * 
     * @param Configuration $oConfig
     * @return \PDO
     * @throws \MiniOrm\DbalException
     */
    public function getDataAccessObject(Configuration $oConfig)
    {
        $sConnect = $this->getConnection($oConfig);

        try {
            return new \PDO($sConnect);
        } catch (\PDOException $oExp) {
            throw DbalException::pdoException($oExp->getMessage(), $oExp);
        }
    }

}
