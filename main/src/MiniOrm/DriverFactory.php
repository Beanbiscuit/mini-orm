<?php

namespace MiniOrm;

use MiniOrm\Driver\Driver;

/**
 * Factory class for instantiating the various implementations of the driver interface.
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class DriverFactory
{

    /**
     * Contains the driver package or sub dirctory name.
     */
    const PACKAGE_NAME = 'Driver';

    /**
     * Returns a concrete instance of a Driver class.
     * 
     * Each driver class encapsulates creating a connection to a specific database.
     * 
     * @param string $sDriver
     * @return \MiniOrm\Driver
     * @throws \MiniOrm\DbalException
     * @throws \RuntimeException
     */
    public function getInstance($sDriver)
    {
        $sClass = '\\' . __NAMESPACE__ . '\\' . self::PACKAGE_NAME . '\\' . $sDriver;

        if (\class_exists($sClass)) {

            $oDriver = new $sClass();

            if ($oDriver instanceof Driver) {

                return $oDriver;
            } else {

                //Code error, throwing runtime exceptin.
                throw new \RuntimeException(
                'Requested driver class does not implement the Driver interface.  Class is ' . $sDriver);
            }
        } else {

            //Incorrect class name provided by client code.
            throw DbalException::driverNotFound($sDriver);
        }
    }

}
