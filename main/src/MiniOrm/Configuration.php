<?php

namespace MiniOrm;

use MiniOrm\DbalException;

/**
 * Configuration class reads package configurations froma file.
 * 
 * ORM can be configured to use a variety of database drivers and other connection settings.
 * 
 * ORM and PDO configurations can be passed to the package via the configuration file.
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class Configuration
{

    /**
     * Allows the cponfiguration object to be used as a singleton.
     */
    use ObjectRegistry;

    /**
     * Array keys value holds dbal connection settings 
     */
    const CONNECTION_KEY = 'connection';

    /**
     * Array keys value holds the orm package settings 
     */
    const ORM_KEY = 'connection';

    /**
     * Array keys value holds additional  options to pass to PDO.
     */
    const PDO_KEY = 'connection';

    /**
     * Array keys value holds a path to the log file.
     */
    const LOG_FILE_KEY = 'logfile';

    /**
     * An array of configurations.
     * 
     * @var array
     */
    private $aConfigs = array();

    /**
     * PRovate construtor, class cannot be directly instantiated.
     */
    private function __construct()
    {
        
    }

    /**
     * This operation reads a configuration file using the file path method argument.
     * 
     * Configurations are stored in a class attribue as an array.
     * 
     * @param string $sPath
     * @throws \MiniOrm\DbalException
     */
    public function read($sPath)
    {
        if (!\is_file($sPath)) {

            throw DbalException::configurationNotFound($sPath);
        }

        if (!\is_readable($sPath)) {

            throw DbalException::configurationNotReadable($sPath);
        }

        $this->aConfigs = include $sPath;

        if (!\is_array($this->aConfigs)) {

            throw DbalException::configurationInvalid($sPath);
        }
    }

    /**
     * Returns the orm configurations multi dimensional array.
     * 
     * @return array
     */
    public function get()
    {

        return $this->aConfigs;
    }

}
