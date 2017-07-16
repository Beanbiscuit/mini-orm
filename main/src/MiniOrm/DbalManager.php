<?php

namespace MiniOrm;

use \MiniOrm\DriverFactory;
use \MiniOrm\Configuration;
use \MiniOrm\DbalException;

/**
 * The database abstraction class allows different databases to be utilized by client code by simply changing connection details and the driver.
 * 
 * The class will instantiate PDO with the provided configurations.
 * 
 * The data access object is then injected into the entity manager.
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class DbalManager
{

    /**
     *
     * @var \MiniOrm\Configuration
     */
    private $oConfig;

    /**
     *
     * @var \MiniOrm\DriverFactory
     */
    private $oDriverFactory;

    /**
     * 
     */
    const ERRMODE_SILENT = 'ERRMODE_SILENT';

    /**
     * 
     */
    const ERRMODE_WARNING = 'ERRMODE_WARNING';

    /**
     * 
     */
    const ERRMODE_EXCEPTION = 'ERRMODE_EXCEPTION';

    /**
     * Constructor requires configuration and driver factory instances.
     * 
     * @param DriverFactory $oFactory
     * @param Configuration $oConfig
     */
    public function __construct(DriverFactory $oFactory, Configuration $oConfig)
    {

        $this->oConfig = $oConfig;
        $this->oDriverFactory = $oFactory;
    }

    /**
     * Configures and then returns a data access object returned from a Driver.
     * 
     * @return \PDO
     * @throws DbalException
     */
    public function getDataObject()
    {

        $aConfig = $this->oConfig->get();

        if (isset($aConfig[Configuration::CONNECTION_KEY]) && isset($aConfig[Configuration::CONNECTION_KEY]['driver'])) {

            $oDriver = $this->oDriverFactory->getInstance($aConfig[Configuration::CONNECTION_KEY]['driver']);
            $oPDO = $oDriver->getDataAccessObject($this->oConfig);
            
            //Configure the error reporting level for PDO.
            $this->setErrorMode($oPDO);

            return $oPDO;
        } else {

            throw DbalException::invalidConfiguration('Cannot find driver in configurations.');
        }
    }

    /**
     * Set the error mode for PDO.
     * 
     * If no error mode is set in configurations then PDO will be set to error mode exception.
     * 
     * @param \PDO $oPDO
     */
    private function setErrorMode(\PDO $oPDO)
    {

        $aConfig = $this->oConfig->get();

        if (isset($aConfig[Configuration::PDO_KEY]) && isset($aConfig[Configuration::PDO_KEY]['errormode'])) {

            switch ($aConfig[Configuration::PDO_KEY]['errormode']) {

                case self::ERRMODE_SILENT:

                    $oPDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
                    break;
                case self::ERRMODE_WARNING:

                    $oPDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
                    break;
                case self::ERRMODE_EXCEPTION:

                    $oPDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    break;
            }
        } else {
            //Default setting if error mode is not set in configurations.
            $oPDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
    }

}
