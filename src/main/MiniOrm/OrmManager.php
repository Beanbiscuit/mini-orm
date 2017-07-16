<?php
namespace MiniOrm;

use MiniOrm\Mapping\AnnotationMapping;
use MiniOrm\Prepare\PrepareStatementImpl;

/**
 * Factory class for injecting all required classes into both the DBAL and entity manager.
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class OrmManager
{

    /**
     * Allows the ROM manager to be used as a singleton
     */
    use ObjectRegistry;

    /**
     *
     * @var string
     */
    private $sConfigPath;

    private function __construct() {}

    /**
     * 
     */
    public function getEntityManager()
    {
        $oDbal = $this->getDbalManager();

        //Grab the types from configuration
        $oPrepare = new PrepareStatementImpl();
        $oUnitOfWork = new UnitOfWork();
        $oMapper = new AnnotationMapping();

        return new EntityManager($oDbal, $oMapper, $oUnitOfWork, $oPrepare);
    }

    /**
     * 
     */
    public function getDbalManager()
    {
        $oConfig = Configuration::getInstance();
        $oConfig->read($this->sConfigPath);
        $oFactory = new DriverFactory();

        return new DbalManager($oFactory, $oConfig);
    }
}