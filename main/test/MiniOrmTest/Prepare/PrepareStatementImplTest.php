<?php

namespace MiniOrmTest\Prepare;

use MiniOrmTest\Entity\ApplicationUser;
use MiniOrm\Mapping\AnnotationMapping;
use MiniOrm\Prepare\PrepareStatementImpl;
use MiniOrm\Configuration;
use MiniOrm\DriverFactory;
use MiniOrm\DbalManager;

/**
 * Description of PrepareStatementImpl
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class PrepareStatementImplTest extends \PHPUnit_Framework_TestCase
{

    /**
     * 
     */
    const MYSQL_TEST_CONFIG = '/../../configuration/orm-mysql-config.php';

    /**
     * 
     */
    public function testPrepareInsert()
    {
        
        $oEntity = new ApplicationUser();
        $oConfig = Configuration::getInstance();
        $oConfig->read(__DIR__ . self::MYSQL_TEST_CONFIG);

        $oDbal = new DbalManager(new DriverFactory(), $oConfig);

        $oMapper = new AnnotationMapping();
        $oMapper->mapEntity(\get_class($oEntity));

        $oPrepare = new PrepareStatementImpl();
        $insert = $oPrepare->insert($oEntity, $oMapper, $oDbal->getDataObject());
    }

}
