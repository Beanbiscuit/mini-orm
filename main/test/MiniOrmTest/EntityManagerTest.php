<?php

namespace MiniOrmTest;

use MiniOrmTest\Entity\ApplicationUser;
use MiniOrm\Mapping\AnnotationMapping;
use MiniOrm\Prepare\PrepareStatementImpl;
use MiniOrm\Configuration;
use MiniOrm\DriverFactory;
use MiniOrm\DbalManager;
use MiniOrm\EntityManager;
use MiniOrm\UnitOfWork;

/**
 * Description of EntityManagerTest
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class EntityManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * 
     */
    const MYSQL_TEST_CONFIG = '/../configuration/orm-mysql-config.php';

    /**
     * !!Problem with date times.  Adjust
     */
    public function testInstantiateAndPersist()
    {

        $oConfig = Configuration::getInstance();
        $oConfig->read(__DIR__ . self::MYSQL_TEST_CONFIG);

        $sName = uniqid('user_');

        //Create some entites to persist in the data layer
        $oEntity = new ApplicationUser();
        $oEntity->setEmail($sName . '@foobar.com');
        $oEntity->setDisplayName($sName . ' display');
        $oEntity->setUsername('domain\\' . $sName);

        $oEntity2 = new ApplicationUser();
        $oEntity2->setEmail($sName . '2@foobar.com');
        $oEntity2->setDisplayName($sName . ' 2display');
        $oEntity2->setUsername('domain2\\' . $sName);

        $oPrepare = new PrepareStatementImpl();
        $oUnitOfWork = new UnitOfWork();
        $oDbal = new DbalManager(new DriverFactory(), $oConfig);
        $oMapper = new AnnotationMapping();

        $oEntityManager = new EntityManager($oDbal, $oMapper, $oUnitOfWork, $oPrepare);

        //Mark the entity as one to be persisted or inserted into the database.
        $oEntityManager->persist($oEntity);
        $oEntityManager->persist($oEntity2);
        $this->assertEquals(2, $oUnitOfWork->count());

        //Synchronize the objects in the unit of work with the database.
        //All entites that were passed to the persist method will also be updated with the relevant id.
        $oEntityManager->flush();
        //Flush should empty the unit of work
        $this->assertEquals(0, $oUnitOfWork->count());

        $this->assertNotNull($oEntity->getId());
        $this->assertNotNull($oEntity2->getId());

        $oPersistEntity = $oEntityManager->find('MiniOrmTest\Entity\ApplicationUser', $oEntity2->getId());
        $this->assertNotNull($oPersistEntity);
        $this->assertEquals($oEntity2->getDisplayName(), $oPersistEntity->getDisplayName());

        print_r($oPersistEntity, 'Entity is persisted');

        $aCollection = $oEntityManager->findAll('MiniOrmTest\Entity\ApplicationUser');
        print_r($aCollection);

        $this->assertNotEmpty($aCollection);
        $this->assertGreaterThan(0, $aCollection);
        //
        $oEntityManager->delete($oEntity);
        $oEntityManager->delete($oEntity2);
//
        $oEntityManager->flush();

        $oRemovedEntity1 = $oEntityManager->find('MiniOrmTest\Entity\ApplicationUser', $oEntity2->getId());
        $this->assertNull($oRemovedEntity1);
    }

}
