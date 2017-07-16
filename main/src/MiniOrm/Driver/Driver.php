<?php

namespace MiniOrm\Driver;

use MiniOrm\Configuration;

/**
 * Interface for database drivers that are available in the mini orm package.
 * 
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
interface Driver
{

    /**
     * Returns the connection string that is to be passed when instantiating the PDO object.

     * @param \MiniOrm\Configuration $oConfig
     * @return string
     */
    public function getConnection(Configuration $oConfig);

    /**
     * Returns an instance of the PHP data object configured using the settings in the configuration file.
     * 
     * @param Configuration $oConfig
     * @throws \MiniOrm\DbalException
     */
    public function getDataAccessObject(Configuration $oConfig);

    /**
     * Converts a PHP date time object into a database specific date time format for the implementing driver.
     * 
     * @param \DateTime $oDateTime
     */
    public function convertDateTime(\DateTime $oDateTime);

    /**
     * Filter the provided string for inclustion in SQL statements.
     * 
     * The method allows database specific filtering to be performed on the provided string.  
     * 
     * @param string $mValue
     */
    public function filter($mValue);
}
