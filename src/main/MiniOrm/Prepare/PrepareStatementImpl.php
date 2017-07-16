<?php
namespace MiniOrm\Prepare;

use MiniOrm\Mapping\EntityMapping;

/**
 * This class uses data returned from entity mapping and prepares an insert statement
 * from the mapping derived from the entity class properties.
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class PrepareStatementImpl implements PrepareStatement
{

    /**
     * Build an SQL insert statement using PDO for the given entity. 
     * 
     * @param object $oEntity - The entity to persist.
     * @param EntityMapping $oMapping - Property to db column mappings for the entity.
     * @param \PDO $sPDO - The data access object.
     * @return object
     * @throws PrepareException
     */
    public function insert($oEntity, EntityMapping $oMapping, \PDO $sPDO)
    {
        //Get the class attribute to table column mapping for the given entity.
        $aMapping = $oMapping->getMappingForEntity($oEntity);

        if (!isset($aMapping[EntityMapping::KEY_COLUMNS]) || empty($aMapping[EntityMapping::KEY_COLUMNS])) {

            //End here if no mappings are found.
            throw PrepareException::emptyColumnMapping(\get_class($oEntity));
        }

        if (!isset($aMapping[EntityMapping::KEY_TABLE]) || empty($aMapping[EntityMapping::KEY_TABLE])) {

            //End if the name of the table is missing
            throw PrepareException::tableNameNotFound(\get_class($oEntity));
        }

        //Grab the column mappings for the provided entity.
        $aColumns = $aMapping[EntityMapping::KEY_COLUMNS];

        //Build the PDO insert string using the attribute to column mapping array.
        $sStatement = $this->buildInsertStatement( $aMapping[EntityMapping::KEY_TABLE], $aMapping[EntityMapping::KEY_COLUMNS]);

        //Prepare the statement
        $oStmt = $sPDO->prepare($sStatement);

        //Bind parameters and values.
        $this->bindParameters($oStmt, $aColumns, $oEntity);

        return $oStmt;
    }

    /**
     * Builds am SQL delete statement for the given entity and mappings.
     *
     * @param object $oEntity
     * @param EntityMapping $oMapping
     * @param \PDO $sPDO
     * @return object
     */
    public function delete($oEntity, EntityMapping $oMapping, \PDO $sPDO)
    {
        $aMapping = $oMapping->getMappingForEntity($oEntity);
        $sIdAttrName = $aMapping[EntityMapping::KEY_PRIMARY_ID][EntityMapping::ATTR_PROPERTY];
        $sTableName = $aMapping[EntityMapping::KEY_TABLE];

        $oReflect = new \ReflectionClass($oEntity);
        $oProperty = $oReflect->getProperty($sIdAttrName);

        $oProperty->setAccessible(true);
        $mId = $oProperty->getValue($oEntity);

        //Escape table name and column names.
        $oStmt = $sPDO->prepare(PrepareStatement::DELETE_STATEMENT . ' ' . $sTableName . ' WHERE ' . $sIdAttrName . ' = :id');
        $oStmt->bindParam(':id', $mId);

        return $oStmt;
    }

    /**
     * Bind the parameters and values to a PDO statement.
     * 
     * @param \PDOStatement $oStmt
     * @param array $aColumns
     * @param object $oEntity
     * @throws PrepareException
     */
    protected function bindParameters(\PDOStatement $oStmt, array $aColumns, $oEntity)
    {
        //Get reflection for the passed entity, REflection is required to get the values of the entity attributes
        $oReflect = new \ReflectionClass($oEntity);

        //Bind parameters
        //Get the values for the entity attributes using reflection here.
        foreach ($aColumns as $aColumn) {

            if (isset($aColumn[EntityMapping::ATTR_NAME]) && isset($aColumn[EntityMapping::ATTR_PROPERTY])) {

                $oProperty = $oReflect->getProperty($aColumn[EntityMapping::ATTR_PROPERTY]);
                $oProperty->setAccessible(true);

                $sKey = ':' . $aColumn[EntityMapping::ATTR_NAME];
                $mValue = $oProperty->getValue($oEntity);

                $oStmt->bindParam($sKey, $mValue, $this->bindType($aColumn), $this->bindLength($aColumn));
                $oStmt->bindValue($sKey, $mValue, $this->bindType($aColumn));
            } else {

                throw PrepareException::annotationAttributeNotFound(\get_class($oEntity));
            }
        }
    }

    /**
     * Checks the mapping array for the column/attribute to see if a length has been provided.
     * 
     * @param array $aColumn
     * @return string | int | null - Returns null if no length has been provided.
     */
    private function bindLength($aColumn)
    {
        $mLength = null;

        if (isset($aColumn[EntityMapping::ATTR_LENGTH])) {

            $mLength = $aColumn[EntityMapping::ATTR_LENGTH];
        }
        return $mLength;
    }

    /**
     * Set the value type for a PDO bind parameter operation.
     *
     * @param array $aColumn - A mapping array for a table column.
     * @return mixed  Retrieves the column type from mapping and assign the relevant PDO TYPE CONSTANT.
     */
    private function bindType($aColumn)
    {
        $sPDOType = \PDO::PARAM_STR;

        //See http://php.net/manual/en/pdo.constants.php for all PDO constants.
        if (isset($aColumn[EntityMapping::ATTR_TYPE])) {

            switch ($aColumn[EntityMapping::ATTR_TYPE]) {
                case 'int':
                    $sPDOType = \PDO::PARAM_INT;
                    break;
                case 'string':
                    $sPDOType = \PDO::PARAM_STR;
                    break;
                case 'bool':
                    $sPDOType = \PDO::PARAM_BOOL;
                    break;
                case 'lob':
                    $sPDOType = \PDO::PARAM_LOB;
                    break;
            }
        }
        return $sPDOType;
    }

    /**
     * Construct the insert statement using placeholders for the value.
     *
     * @param string $sTable
     * @param array $aColumns
     * @return string - The insert statement string for the PDO prepare method.
     */
    private function buildInsertStatement($sTable, $aColumns)
    {
        $sColumnName = '';
        $sPlaceholders = '';

        foreach ($aColumns as $aColumn) {

            if (isset($aColumn[EntityMapping::ATTR_NAME])) {

                //Add the column names to the insert statement.
                $sColumnName .= $aColumn[EntityMapping::ATTR_NAME] . ', ';
                //Build placeholders for bindParam operations.
                $sPlaceholders .= ':' . $aColumn[EntityMapping::ATTR_NAME] . ', ';
            }
        }
        return PrepareStatement::INSERT_STATEMENT . ' ' . $sTable . ' (' . \rtrim($sColumnName, ', ') . ')' .
            ' VALUES (' . \rtrim($sPlaceholders, ', ') . ')';
    }
}