<?php
namespace MiniOrm\Mapping;

/**
 * Exception class for catching entity mapping exceptions
 * 
 * Provides static methods for exceptions that are common to all implementations of the MApping interface.
 * 
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class MappingException extends \Exception
{

    /**
     * Thrown when a table name is missing or invalid when mapping an Entity
     * .
     * @param string $sClass - The name of the entity.
     * @return \self
     */
    public static function invalidTableName($sClass)
    {
        return new self('Cannot map the database table name.  Name is missing or invalid in mappings. ' . $sClass);
    }

    /**
     * Thrown when an object is passed to a mapper but is not tagged as an entity.
     * 
     * @return \self
     */
    public static function invalidEntity()
    {
        return new self('Object passed to mapper is not a valid ORM entity.  Please Check mappings.');
    }

    /**
     * Thrown when client code attempts to lookup up a classname that does not exist in the mapping array.
     * 
     * @param string $sClassName
     * @return \self
     */
    public static function entityNotMapped($sClassName)
    {
        return new self('Cannot find the provided entity name in mappings.  Class is ' . $sClassName);
    }
}