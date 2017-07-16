<?php

namespace MiniOrmTest\Driver;

use MiniOrm\Configuration;
use MiniOrm\DriverFactory;
use MiniOrm\Driver\MySql;
/**
 * MySql driver test cases.
 * 
 * Including @covers annotation for code coverage reports.
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class MySqlTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Configuration
     */
    private $oConfig;

    /**
     *
     * @var MySql
     */
    private $object;

    /**
     * 
     */
    const MYSQL_TEST_CONFIG = '/../../configuration/orm-mysql-config.php';

    /**
     * 
     */
    const MYSQL_TEST_EMPTY_CONFIG = '/../../configuration/orm-empty-config.php';

    /**
     * 
     */
    public function setUp()
    {

        $this->oConfig = Configuration::getInstance();
        $this->oConfig->read(__DIR__ . self::MYSQL_TEST_CONFIG);

        $oFactory = new DriverFactory();
        $this->object = $oFactory->getInstance('MySql');
    }

    /**
     * @covers \MiniOrm\Driver\MySql::getConnection
     */
    public function testGetConnection()
    {
        $sConnect = $this->object->getConnection($this->oConfig);
        $this->assertNotNull($sConnect);
        $this->assertEquals('mysql:host=192.168.186.128;dbname=my_helpdesk', $sConnect);
    }

    /**
     *  @covers \MiniOrm\Driver\MySql::getDataAccessObject
     */
    public function testGetDataAccessObject()
    {

        $oPDO = $this->object->getDataAccessObject($this->oConfig);
        $this->assertNotNull($oPDO);
        $this->assertInstanceOf('\PDO', $oPDO);
    }

    /**
     * @expectedException \MiniOrm\DbalException
     */
    public function testGetInvalidConnection()
    {
        $oConfig = Configuration::getInstance();
        $oConfig->read(__DIR__ . self::MYSQL_TEST_EMPTY_CONFIG);
        $this->object->getConnection($oConfig);
    }

}
