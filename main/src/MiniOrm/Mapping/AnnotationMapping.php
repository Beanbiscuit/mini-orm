<?php

namespace MiniOrm\Mapping;

use MiniOrm\Mapping\MappingException;

/**
 * This class maps an entity to the associated database table by reading annotations placed in the class 
 * and property doc blocks.
 * 
 * Mini ORM annotations use the annotations defined by the Java Persistence API however only limited 
 * attributes for the various annotations are implemented by mini ORM.
 * 
 * Replace the @ with @ORM\ for each annotation in order to provide compatabilty with the Doctrine ORM.
 * 
 * Annotations for columns must be added to class attributes.
 * All annotation attribute values must be enclosed by single commas.
 * Each annotation must be declared on a new line in the doc block.  Do not place multiple annotations 
 * on the same line.
 * 
 * Add @Entity to the top of every entity.
 * Add @Table(table_name='db_table_name')
 * 
 * Mini ORM only supports auto incrementing primary keys of numeric type e.g. INTEGER, BIGINT etc.  
 * 
 * Add @Id(name='db_table_column_name', type="some type") to a class property to map it to the database 
 * table primary key column.
 * 
 * A custom attribute is used by the package for the @Column annotation and is called type.
 * Use the name of a php primitive or the class name of an associated entity as the value.
 * 
 * Map attriubtes to columns by adding.
 * 
 * @Column(name='db_table_column_name', type="int") - maps to column types BIGINT, SMALLINT etc
 * @Column(name='db_table_column_name', type="string") - maps to column types CHAR, VARCHAR
 * @Column(name='db_table_column_name', type="bool") - maps to column type TINYINT etc
 * 
 * Class properties not marked with the @Column annotation are ignored.
 * Class methods uses the PHP Reflection API to read annotations.
 * 
 * Class methods are marked as protected so a custom implementation of this case can be created by extending 
 * this class.
 * 
 * @todo Add @ManaytoManay and other annotations marking associations between entites/tables.
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class AnnotationMapping implements EntityMapping
{

    /**
     * Holds a map of all entites passed to the class during its lifetime.
     * 
     * @var array
     */
    private $aMapping;

    /**
     * 
     */
    const TAG_PRIMARY_KEY = '@Id';

    /**
     * 
     */
    const TAG_ENITIY = '@Entity';

    /**
     * 
     */
    const TAG_COLUMN = '@Column';

    /**
     * 
     */
    const TAG_JOIN_COLUMN = '@JoinColumn';

    /**
     * 
     */
    const TAG_TABLE = '@Table';

    /**
     * 
     */
    const TAG_ONE_TO_ONE = '@OneToOne';

    /**
     * 
     */
    const TAG_MANY_TO_ONE = '@ManyToOne';

    /**
     * 
     */
    const TAG_MANY_TO_MANY = '@ManyToMany';

    /**
     * 
     */
    const TAG_ONE_TO_MANY = '@OneToMany';

    /**
     * Maps the provided entity to a database table.
     * 
     * Maps an entities class attributes to the related database table columns.
     * 
     * @param string $sEntityName
     * @throws MappingException
     */
    public function mapEntity($sEntityName)
    {

        //$sClassName = \get_class($oEntity);
        $oReflect = new \ReflectionClass($sEntityName);
        $sDocBlock = $oReflect->getDocComment();

        //Onlt attempt to map the provided object if it is marked as an ORM entity.
        if ($this->isEntity($sDocBlock)) {

            //The method will only need to read the entity mapping once in the lifetime of the script so
            //do not map the entity if it has already been added to the map.
            if (!isset($this->aMapping[$sEntityName])) {

                $this->aMapping[$sEntityName] = array();
                $this->parseTableTag($sEntityName, $sDocBlock);

                $aProperties = $oReflect->getProperties(
                        \ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);

                $this->mapProperties($sEntityName, $aProperties);
            }
        } else {

            throw MappingException::invalidEntity();
        }
    }

    /**
     * Pass the an entity to this method to return mapping data for the entity.
     * 
     * @param object $oEntity
     * @return array
     * @throws MappingException
     */
    public function getMappingForEntity($oEntity)
    {

        $sClassName = \get_class($oEntity);

        if (isset($this->aMapping[$sClassName])) {

            return $this->aMapping[$sClassName];
        } else {

            throw MappingException::entityNotMapped($sClassName);
        }
    }

    /**
     * Pass the class name of an entity to this method to return mapping data for the entity.
     * 
     * @param string $sEntity - The class name of the ORM entity including namespace.
     * @return array - The mapping array for the provided entity
     */
    public function getMappingForEntityName($sEntity)
    {
        if (isset($this->aMapping[$sEntity])) {
            
            return $this->aMapping[$sEntity];
        }
        
        return null;
    }

    /**
     * Returns the mapping array.
     * 
     * @return array
     */
    public function getAllMappings()
    {

        return $this->aMapping;
    }

    /**
     * Loop over each class property and scans for ORM annotations.
     * 
     * @param object $oEntity
     * @param string The full class name of the entity whose proerties are to be mapped.
     * @param array $aProperties - An array of Property objects.
     */
    protected function mapProperties($sClassName, $aProperties)
    {

        $this->aMapping[$sClassName][self::KEY_COLUMNS] = array();

        //Class attributes not marked with a @Id or @Column tag are ommited from the map..
        foreach ($aProperties as $oProperty) {

            $oProperty->setAccessible(true);
            if ($this->tagExists($oProperty->getDocComment(), self::TAG_COLUMN)) {

                //Return an array which maps the class property to the db table column.
                $this->aMapping[$sClassName][self::KEY_COLUMNS][] = $this->parseColumnTag($oProperty);
            } elseif ($this->tagExists($oProperty->getDocComment(), self::TAG_PRIMARY_KEY)) {

                //Grab the declared primary key if one exists.
                $this->parsePrimaryKeyTag($oProperty, $sClassName);
            }
        }
    }

    /**
     * Checks if a provided tag exists within a provided doc block.
     * 
     * Method is marked as protected so this class can be extended.
     * 
     * @param string $sDocBlock
     * @param string $sTag
     */
    protected function tagExists($sDocBlock, $sTag)
    {

        return (\strpos($sDocBlock, $sTag) !== false);
    }

    /**
     * Method checks to see if the passed documentation string contains the @Entity annotation.
     * 
     * Returns true denoting that the passed object is marked as an ORM entity.
     * 
     * @param string $sDocBlock
     */
    protected function isEntity($sDocBlock)
    {

        return $this->tagExists($sDocBlock, self::TAG_ENITIY);
    }

    /**
     * Parses the promary key annotation for the provided entity and extracts annotation attributes.
     * 
     * @param \ReflectionProperty $oProperty
     * @param string $sClassName
     */
    protected function parsePrimaryKeyTag($oProperty, $sClassName)
    {

        $aMap = array();
        $aMap['property'] = $oProperty->getName();

        $sIdTag = $this->getTag(self::TAG_PRIMARY_KEY, $oProperty->getDocComment());

        //Remove all chars and tag name from the tag attributes.
        $sTagAttributes = \str_replace(array(self::TAG_PRIMARY_KEY, '(', ')'), '', $sIdTag);

        //Split on the comma to get each attribute into an array index.
        $aColumnMap = $this->parseTagAttributes($aMap, $sTagAttributes);
        //Assign the mapping array for the primary key to the main mappings array.
        $this->aMapping[$sClassName][self::KEY_PRIMARY_ID] = $aColumnMap;
    }

    /**
     * Grab the name of the database table from the @Table tag that is to be mapped to a provided entity.
     * 
     * @param string $sClassName - The class name of the entity that is to be mapped including namespace.
     * @param string $sDocBlock - The class documentation block string.
     * @throws MappingException
     */
    protected function parseTableTag($sClassName, $sDocBlock)
    {

        if ($this->tagExists($sDocBlock, self::TAG_TABLE)) {

            //Returns athe provided ORM tag from the given documentation block string.
            $sTableTag = $this->getTag(self::TAG_TABLE, $sDocBlock);

            //Check the return, if empty or other then throw a mapping exception.
            if (!\is_string($sTableTag) && empty($sTableTag)) {

                throw MappingException::invalidTableName($sClassName);
            }

            //Only expect the name attribute so remove all surrounding strings from the TAG string
            //Using string manipulation as it is more efficient then regex.
            $sTableName = \str_replace(array(
                self::TAG_TABLE, 'name', '\'', ',', '=', '(', ')', '"'), '', $sTableTag);

            //Trim any spaces from around the value
            $this->aMapping[$sClassName][self::KEY_TABLE] = \trim($sTableName);
        }
    }

    /**
     * Parses values from a @Column tag attributes and adds them to an array.
     * 
     * The array will be used by PDO to determine the database column type.
     * 
     * @param \ReflectionProperty $oProperty - A PHP class Property object from the REflection API.
     * @return array - An array of ORM attributes derived from a @Column annotation.
     */
    protected function parseColumnTag($oProperty)
    {
        $aMap = array();
        $aMap['property'] = $oProperty->getName();
        $sDocblock = $oProperty->getDocComment();

        //Get the column tag string 
        $sTag = $this->getTag(self::TAG_COLUMN, $sDocblock);

        //Format the tag string, remove the annotation and brackets so that only the attributes remain 
        //in the format ,key='value'
        $sTagAttributes = \str_replace(array(self::TAG_COLUMN, '(', ')'), '', $sTag);

        //Pass to the parse tag attributes method.
        $aColumnMap = $this->parseTagAttributes($aMap, $sTagAttributes);

        //Add the object properties value to the mapping array.
        //$aColumnMap['value'] = $oProperty->getValue($oEntity);
        return $aColumnMap;
    }

    /**
     * Grab attributes and pass into an array as key value pairs from the provided tag attributes string.
     * 
     * @param array $aMap
     * @param string $sTagAttributes
     * @return array
     */
    protected function parseTagAttributes($aMap, $sTagAttributes)
    {
        //Split on the comma to get each attribute into an array index.
        $aAttribute = \split(',', $sTagAttributes);

        foreach ($aAttribute as $sAttribute) {

            //Remove commas around the attributes value.
            $sCleanAttr = \str_replace(array('\'', '"'), '', $sAttribute);

            //Split on the assignment char to get the attribute name as an array key and attribute value as the 
            //array key value.      
            $aKeyValue = \split('=', $sCleanAttr);

            //Check that the value returned by split is an array with 2 entries.
            if (\is_array($aKeyValue) && 2 == \count($aKeyValue)) {

                //Trim any spaces in the annotation attribute name
                $sKey = \trim($aKeyValue[0]);
                $aMap[$sKey] = $aKeyValue[1];
            }
        }
        return $aMap;
    }

    /**
     * Strips the required tag from the provided docblock string.
     * 
     * The method uses the passed tag to denote the start of the ORM annotation.
     * The method uses the * char to denote the end of the ORM annotation.
     * 
     * @param string $sTagName
     * @param string $sDocBlock
     * @return string
     * @throws MappingException
     */
    protected function getTag($sTagName, $sDocBlock)
    {

        $sTag = null;

        //Grab a sub string from the passed doc string that starts with the provided tag string.
        $sTagStart = \stristr($sDocBlock, $sTagName, false);

        //Looking to split on the * char
        //Escaping so the first args value is not seen as regex by the PHP compiler.
        $aSeg = \split('\*', $sTagStart);

        //First array index after split should contain the tag with attributes.
        if (\is_array($aSeg) && isset($aSeg[0])) {

            $sTag = $aSeg[0];
        }

        //Check if the returned Tag string has any extra @ chars, this indicates that the tag is malformed
        //or contains multiple annotations.
        if (\substr_count($sTag, '@') > 1) {

            //Exception only related to this class so no static exception methods are declared for annotation 
            //parsing errors..
            $sMessage = 'Multiple annotations declared on the same doc block line in classs ';
            $sMessage .= ' docblock decleration is ' . $sDocBlock;
            throw new MappingException($sDocBlock);
        }

        return $sTag;
    }

}
