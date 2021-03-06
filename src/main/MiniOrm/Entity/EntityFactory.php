<?php
namespace MiniOrm\Entity;

use MiniOrm\Mapping\EntityMapping;

/**
 * Static factory class for instantiating entities based on mappings and values returned from the
 * data layer
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
final class EntityFactory
{

    /**
     * Class is stateless, contains only static methods.
     * 
     * Client code should not be able to instantiate the factory.
     */
    private function __construct() {}

    /**
     * Instantiates a managed entity given its name, a hash table of attribute names and associated values and also the
     * mapping data for the entity.
     * 
     * @param string $sClassName - The full class name including the namespace.
     * @param array $aEntityMap - A hash table of attribute names and associated values.  Used to construct the entity.
     * @return object - A plain ol PHP that is declared as an ORM entity.
     */
    public static function newInstance($sClassName, $aEntityMap, $aMapping)
    {
        $oEntity = null;

        if (\class_exists($sClassName)) {

            if (!is_null($aEntityMap) && !empty($aEntityMap)) {

                $oEntity = self::buildEntity($sClassName, $aEntityMap, $aMapping);
            }
        }

        return $oEntity;
    }

    /**
     * Instantiates an entity given the entity name, values returned from the table row that the entities will
     * encapsulate and the ORM mapping data which links the entities attributes and the associated database table
     * columns.
     * 
     * @param string $sClassName
     * @param array $aEntityMap
     * @return object - A plain PHP that is declared as an ORM entity.
     */
    protected static function buildEntity($sClassName, $aEntityMap, $aMapping)
    {
        $oReflect = new \ReflectionClass($sClassName);
        $oInstance = $oReflect->newInstanceWithoutConstructor();
        $aColumns = $aMapping[EntityMapping::KEY_COLUMNS];

        foreach ($aColumns as $aColumn) {

            $oProperty = $oReflect->getProperty($aColumn[EntityMapping::ATTR_PROPERTY]);
            $oProperty->setAccessible(true);

            $mValue = null;
            if ('datetime' == $aColumn[EntityMapping::ATTR_TYPE]) {

                $mValue = new \DateTime($aEntityMap[$aColumn[EntityMapping::ATTR_NAME]]);
            } else {

                $mValue = $aEntityMap[$aColumn[EntityMapping::ATTR_NAME]];
            }

            $oProperty->setValue($oInstance, $mValue);
        }
        return $oInstance;
    }
}