<?php

namespace MiniOrmTest;

use MiniOrm\DriverFactory;

/**
 * Description of DriverFactoryTest
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class DriverFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Tests instantiation for a Driver class given the Drivers class name.
     */
    public function testGetInstance()
    {

        $oFactory = new DriverFactory();
        $oDriver = $oFactory->getInstance('MySql');

        $this->assertInstanceOf('\MiniOrm\Driver\MySql', $oDriver);
    }

    /**
     * Tests instantiation for an invliad driver name.
     * 
     * @expectedException \MiniOrm\DbalException
     */
    public function testGetInvalidInstance()
    {

        $oFactory = new DriverFactory();
        $oDriver = $oFactory->getInstance('FOOBAR');

        $this->assertInstanceOf('\MiniOrm\Driver\MySql', $oDriver);
    }

}
