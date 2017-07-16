<?php
namespace MiniOrm\Logging;

use \MiniOrm\Configuration;

/**
 * Trait provides login functionality to all classes that use it.
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
trait PackageLogger
{

    /**
     * Holds the package configuration object.
     * 
     * @var \MiniOrm\Configuration
     */
    private $oConfig;

    /**
     * Clients should call this method in order to enable logging.
     * 
     * @param \MiniOrm\Configuration $oConfig
     */
    public function enableLogging($oConfig)
    {
        $this->oConfig = $oConfig;
    }

    /**
     * Write an entry to the ORM log.
     * 
     * Pass a method or class name as the second argument.
     * 
     * @param string $sMessage
     * @param string $sClass
     * @throws \Exception
     */
    public function log($sMessage, $sClass)
    {
        if ($this->oConfig instanceof Configuration) {

            $aConfig = $this->oConfig->get();

            if (isset($aConfig[Configuration::ORM_KEY]['logfile']) && \is_file($aConfig[Configuration::ORM_KEY]['logfile']) && \is_writable($aConfig[Configuration::ORM_KEY]['logfile'])) {

                $sLog = (\is_string($sClass)) ? $sClass . ' ' . $sMessage : $sMessage;

                //Write to the log file if it exists.  Append entries and apply a lock to the file during write operation.
                \file_put_contents(
                        $aConfig[Configuration::ORM_KEY]['logfile'], $sLog, FILE_APPEND | LOCK_EX);
            } else {

                Throw new \Exception('Log file cannot be found or is not writable');
            }
        }
    }
}