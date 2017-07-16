<?php

namespace MiniOrmTest\Mapping;

use MiniOrmTest\Entity\ApplicationUser;
use MiniOrm\Mapping\AnnotationMapping;

/**
 * Description of AnnotationMappingTest
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class AnnotationMappingTest extends \PHPUnit_Framework_TestCase
{

    /**
     * 
     */
    public function testMapEntityAttributesToColumns()
    {

        $oEntity = new ApplicationUser();
        $oEntity->setId(1);
        $oEntity->setEmail('foobar@foobar.com');
        $oEntity->setDisplayName('FOO BAR');
        $oEntity->setUsername('foobar');
        $oEntity->setCreatedOn(new \DateTime('now'));

        $sClassname = \get_class($oEntity);
        $oMapper = new AnnotationMapping();

        $oMapper->mapEntity(\get_class($oEntity));

        $aMappings = $oMapper->getAllMappings();

        //View the returned array first before assertions.
        print_r($aMappings);

        $this->assertInternalType('array', $aMappings);
        $this->assertArrayHasKey($sClassname, $aMappings);

        $aEntityMap = $aMappings[$sClassname];

        //View the returned array first before assertions.
        print_r($aEntityMap);

        $this->assertNotEmpty($aEntityMap);
        $this->assertArrayHasKey(AnnotationMapping::KEY_TABLE, $aEntityMap);
        $this->assertArrayHasKey(AnnotationMapping::KEY_COLUMNS, $aEntityMap);
    }

}
