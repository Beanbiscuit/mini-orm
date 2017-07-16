<?php

namespace MiniOrmTest;

use MiniOrm\Configuration;
use MiniOrm\DriverFactory;
use MiniOrm\DbalManager;

/**
 * Description of DbalManagerTest
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class DbalManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var DbalManager
     */
    private $object;

    /**
     * 
     */
    const MYSQL_TEST_CONFIG = '/../configuration/orm-mysql-config.php';

    /**
     * 
     */
    public function testGetPDOMySQLConnection()
    {
        $oConfig = Configuration::getInstance();
        $oConfig->read(__DIR__ . self::MYSQL_TEST_CONFIG);
        $oFactory = new DriverFactory();

        $this->object = new DbalManager($oFactory, $oConfig);

        $oDao = $this->object->getDataObject();
        $this->assertInstanceOf('\PDO', $oDao);
    }

}
