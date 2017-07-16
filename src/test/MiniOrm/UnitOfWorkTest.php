<?php
namespace MiniOrm;

use MiniOrm\Entity\ApplicationUser;

/**
 * Description of UnitOfWorkTest
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class UnitOfWorkTest extends \PHPUnit_Framework_TestCase
{

    /**
     * 
     * @covers \MiniOrm\UnitOfWork::registerNew
     */
    public function testRegisterEntity()
    {
        $oUnitOfWork = UnitOfWork::getInstance();
        $oEntity = new ApplicationUser();

        $oUnitOfWork->registerNew($oEntity);
        print_r($oUnitOfWork->getRegistry());
        
        $this->assertEquals(1,$oUnitOfWork->count());
        
        $oUnitOfWork->clear();
        
        $this->assertEquals(0,$oUnitOfWork->count());
    }
    
    /**
     * 
     * @covers \MiniOrm\UnitOfWork::registerNew
     * @expectedException \MiniOrm\OrmException
     */
    public function testRegisterInvalidEntity(){
        $oUnitOfWork = UnitOfWork::getInstance();
         $oUnitOfWork->registerNew(null);
    }

}
