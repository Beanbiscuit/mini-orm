<?php

namespace MiniOrmTest;

use \MiniOrm\Configuration;

class A {

    private $b;

    public function getB() {
        return $this->b;
    }

    public function setB($b) {
        $this->b = $b;
    }

}

class B {

    private $message;

    public function set($sMessage) {

        $this->message = $sMessage;
    }

    public function getMessage() {
        return $this->message;
    }

}

/**
 * Description of ConfigurationTest
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase {

    /**
     * 
     */
    const MYSQL_TEST_CONFIG = '/../configuration/orm-mysql-config.php';

    /**
     * 
     */
    const SQLITE_TEST_CONFIG = '/../configuration/orm-sqlite-config.php';

    /**
     * 
     */
    public function testReadConfigurationFile() {

        $oConfig = Configuration::getInstance();

        //Package uses PHP 5.5 as the minimum langauge but can be refactored to use 5.6 new features..
        //As of 5.6 The __DIR__ can be concantinated to the static property decleration upon instantiation of the test class.
        $oConfig->read(__DIR__ . self::MYSQL_TEST_CONFIG);

        $this->assertArrayHasKey(Configuration::CONNECTION_KEY, $oConfig->get());
        $this->assertArrayHasKey(Configuration::ORM_KEY, $oConfig->get());
    }

    /**
     * @expectedException \MiniOrm\DbalException
     */
    public function testConfigurationFileNotFound() {

        $oConfig = Configuration::getInstance();
        $oConfig->read('/foobar/foobar/bar');
    }

    /**
     * @expectedException \MiniOrm\DbalException
     */
    public function testConfigurationFileNotReadable() {

        $oConfig = Configuration::getInstance();
        $oConfig->read('/usr/bin');
    }

    /**
     * @expectedException \MiniOrm\DbalException
     */
    public function testConfigurationFileInvalid() {

        $oConfig = Configuration::getInstance();
        $oConfig->read(null);
    }

    /**
     * 
     */
    public function testEmptyConfiguration() {

        $oConfig = Configuration::getInstance();
        $oConfig->read(__DIR__ . '/../configuration/orm-empty-config.php');
    }

    public function testReferrences() {

        print 'Pass by Referrence';
        $a = new A();
        $b = new B();

        $b->set('FOO');

        $a->setB($b);

        //B is assigned to a property of A 
        $b->set('Bar');

        print 'Pass By Referrence, should be Bar, is ' . $a->getB()->getMessage();
        $this->assertEquals($a->getB()->getMessage(), 'Bar');
    }

}
