<?php
namespace MiniOrm\Driver;

use MiniOrm\Configuration;
use MiniOrm\DriverFactory;

/**
 * Description of SqliteTest
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class SqliteTest extends \PHPUnit_Framework_TestCase
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
    const TEST_CONFIG = '/../../configuration/orm-sqlite-config.php';

    /**
     * 
     */
    const TEST_EMPTY_CONFIG = '/../../configuration/orm-empty-config.php';

    /**
     * 
     */
    public function setUp()
    {

        $this->oConfig = Configuration::getInstance();
        $this->oConfig->read(__DIR__ . self::TEST_CONFIG);

        $oFactory = new DriverFactory();
        $this->object = $oFactory->getInstance('Sqlite');
    }

    /**
     * @covers \MiniOrm\Driver\MySql::getConnection
     */
    public function testGetConnection()
    {
        $sConnect = $this->object->getConnection($this->oConfig);
        $this->assertNotNull($sConnect);
        $this->assertEquals('sqlite:/path/to/sqlite/database/file/my_helpdesk.sql', $sConnect);
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
        $oConfig->read(__DIR__ . self::TEST_EMPTY_CONFIG);
        $this->object->getConnection($oConfig);
    }

}
