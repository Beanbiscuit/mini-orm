<?php
namespace MiniOrm\Prepare;

/**
 * Exception class for all classes that prepare statments that are to be executed by PDO.
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class PrepareException extends \Exception
{

    /**
     * Thrown when a requested entity name cannot be found in the mapping array.
     * 
     * @param string $sEntity
     * @return \self
     */
    public static function entityNotFound($sEntity)
    {
        return new self('Provided entity name does not exist in mappings. ' . $sEntity);
    }

    /**
     * Thrown when the mapping information of an entity does not exist.
     * 
     * @param string $sEntity
     * @return \self
     */
    public static function emptyColumnMapping($sEntity)
    {
        return new self('Provided entity name does not exist in mappings. ' . $sEntity);
    }

    /**
     * Thrown when the name of the database table is iommited from an entity map.
     * 
     * @param string $sEntity
     * @return \self
     */
    public static function tableNameNotFound($sEntity)
    {
        return new self('Cannot find the associated table name for the provided entity. ' . $sEntity);
    }

    /**
     * Thrown when the name of the database table is iommited from an entity map.
     * 
     * @param string $sEntity
     * @return \self
     */
    public static function annotationAttributeNotFound($sEntity)
    {
        return new self('Cannot find required attribute for an entity. ' . $sEntity);
    }
}
