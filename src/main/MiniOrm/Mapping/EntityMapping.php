<?php
namespace MiniOrm\Mapping;

/**
 * Interface for mapping classes.  Interface allows mapping information to be provided 
 * in a number of ways from annotations to yaml mapping.
 * 
 * Mapping data for each entity should include the following entires.
 * 
 * tableName - The name of the database table that the entity is to be mapped to.
 * columns - An array of attribute and table column data for performing inserts and updates.
 * primaryKey - An array of attributes that map a class attribute to a priomaruy key column in the associated database table.
 * associations e.g. one to one, one to many etc.
 * 
 * Entity attribute to column mappings
 * -----------------------------------
 * 
 * Include the following attributes for an attribute to column mapping.
 * 
 * property - The name of the class attribute.
 * name - The name of the database table column to map the class attribute to.
 * type - The attribute type can be one of the following values - bool | string | int | lob.
 * 
 * Optional attributes are;
 * 
 * length - The length of the field as defined in the table definition. 
 * 
 * Primary key mapping attributes
 * ------------------------------
 * 
 * property - The name of the class attribute.
 * name - The name of the database table column to map the class attribute to.
 * type - The attribute type can be one of the following values - bool | string | int | lob.
 * 
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
interface EntityMapping
{

    ////////////////////////////////////////////////////////////////////////////////////////////////
    // Interface constants define the expected keys in the mapping array that all implementing classes 
    // will return.
    //
    // Use these constants to build the mapping array in custom implementations of the EntityMapping 
    // interface.
    ////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * 
     */
    const KEY_COLUMNS = 'columns';

    /**
     * 
     */
    const KEY_TABLE = 'tableName';

    /**
     * 
     */
    const KEY_PRIMARY_ID = 'primaryId';

    /**
     * 
     */
    const KEY_ASSOCIATIONS = 'associations';

    /**
     * 
     */
    const KEY_ONE_TO_ONE = 'oneToOne';

    /**
     * 
     */
    const KEY_MANY_TO_ONE = 'manyToOne';

    /**
     * 
     */
    const KEY_ONE_TO_MANYD = 'oneToMany';

    /**
     * 
     */
    const KEY_MANY_TO_MANY = 'manyToMany';

    /**
     * 
     */
    const ATTR_NAME = 'name';

    /**
     * 
     */
    const ATTR_PROPERTY = 'property';

    /**
     * 
     */
    const ATTR_TYPE = 'type';

    /**
     * 
     */
    const ATTR_LENGTH = ' length';

    /**
     * Pass the class name of a POPO in order to add its attributes to the map.
     * 
     * @param string $sClassName
     * @throws MappingException
     */
    public function mapEntity($sClassName);

    /**
     * Pass the class name of an entity to this method to return mapping data for the entity.
     * 
     * @param object $oEntity
     * @return array
     * @throws MappingException
     */
    public function getMappingForEntity($oEntity);

    /**
     * Pass the class name of an entity to this method to return mapping data for the entity.
     * 
     * @param object $sEntity
     * @return array
     * @throws MappingException
     */
    public function getMappingForEntityName($sEntity);

    /**
     * Returns the entire mapping array.
     * 
     * @return array
     */
    public function getAllMappings();
}
