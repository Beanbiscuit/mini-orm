<?php
namespace MiniOrm;

use MiniOrm\Mapping\EntityMapping;
use MiniOrm\Prepare\PrepareStatement;
use MiniOrm\Entity\EntityFactory;
use MiniOrm\Logging\PackageLogger;
use MiniOrm\Query\Query;

/**
 * The mini ORM entity manager class.
 * 
 * Use the entity manager to persist and return entites from the database.
 * 
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class EntityManager
{

    /**
     * ORM package logger mixin.
     */
    use PackageLogger;

    /**
     *
     * @var DbalManager
     */
    private $oDbal;

    /**
     *
     * @var EntityMapping
     */
    private $oMapper;

    /**
     *
     * @var UnitOfWork
     */
    private $oUnitOfWork;

    /**
     *
     * @var PrepareStatement
     */
    private $oPrepare;

    /**
     *
     * @var string | int
     */
    private $mLastInsertID;

    /**
     * Construct the entity manager.
     *
     * Provide the the dbal object and mapping object.
     *
     * @param DbalManager $oDbalManager
     * @param EntityMapping $oMapper
     * @param UnitOfWork $oUnitOfWork
     * @param PrepareStatement $oPrepare
     */
    public function __construct(DbalManager $oDbalManager, EntityMapping $oMapper, UnitOfWork $oUnitOfWork, PrepareStatement $oPrepare)
    {
        $this->oDbal = $oDbalManager;
        $this->oMapper = $oMapper;
        $this->oUnitOfWork = $oUnitOfWork;
        $this->oPrepare = $oPrepare;
    }

    /**
     * This method will insert the provided entity into the database.
     * 
     * @param object $oEntity
     */
    public function persist($oEntity)
    {
        $this->oMapper->mapEntity(\get_class($oEntity));
        $this->oUnitOfWork->registerNew($oEntity);
    }

    /**
     * This method will update the provided entity into the database.
     * 
     * @param object $oEntity
     */
    public function update($oEntity)
    {
        $this->oUnitOfWork->registerDirty($oEntity);
    }

    /**
     * This method will delete the provided entity into the database.
     * 
     * @param object $oEntity
     */
    public function delete($oEntity)
    {
        $this->oUnitOfWork->registerRemoved($oEntity);
    }

    /**
     * Provide a query object to the method in order to perform an SQL SELECT query.
     *
     * @param Query $oQuery
     * @throws \Exception
     */
    public function query(Query $oQuery)
    {
        throw new \Exception('Unsupported operation, not available in this release.  For query class ' . \get_class($oQuery));
    }

    /**
     * Fetch a single instance of the provided entity from the database.
     *
     * @param string $sEntityName - The class name of the requested 
     * @param mixed $mId
     * @return object|null
     * @throws OrmException
     */
    public function find($sEntityName, $mId)
    {
        $oDao = $this->oDbal->getDataObject();
        $this->oMapper->mapEntity($sEntityName);
        $aMapping = $this->oMapper->getMappingForEntityName($sEntityName);

        $sPrimaryAttr = $aMapping[EntityMapping::KEY_PRIMARY_ID][EntityMapping::ATTR_NAME];
        $aTable = $aMapping[EntityMapping::KEY_TABLE];

        try
        {
            $oStmt = $oDao->prepare("select * from `{$aTable}` where `{$sPrimaryAttr}` = :id");
            $oStmt->execute(array(':id' => $mId));

            $oRow = $oStmt->fetch(\PDO::FETCH_ASSOC);
            $oStmt->closeCursor();

            return EntityFactory::newInstance($sEntityName, $oRow, $aMapping);
        } catch (\PDOException $oExp)
        {

            OrmException::pdoException('Error fetching all for ' . $sEntityName, $oExp);
        }
        return null;
    }

    /**
     * Performs a find all SQL query for the provided entity.
     * 
     * @param string - The class name of an entity.
     * @return array - An array of entities returned from the find all query.  Returns an empty array if no records are found.
     * @throws OrmException
     */
    public function findAll($sEntityName)
    {
        $oDao = $this->oDbal->getDataObject();
        $this->oMapper->mapEntity($sEntityName);

        $aMapping = $this->oMapper->getMappingForEntityName($sEntityName);
        $aTable = $aMapping[EntityMapping::KEY_TABLE];

        try
        {
            $oStmt = $oDao->prepare("select * from {$aTable}");
            $oStmt->execute();

            $aRows = $oStmt->fetchAll(\PDO::FETCH_ASSOC);
            return $this->buildCollection($sEntityName, $aRows, $aMapping);
        } catch (\PDOException $oExp)
        {

            OrmException::pdoException('Error fetching all for ' . $sEntityName, $oExp);
        }
    }

    /**
     * Loop over rows returned from a query and instantiate the requested entity for each row using
     * the mapping array and entity name provided as arguments.
     *
     * @param string $sEntityName - An entity name.
     * @param array $aRows - An array or results from a SELECT query.
     * @param array $aMapping - The mapping array for the provided entity name.
     * @return array
     */
    private function buildCollection($sEntityName, $aRows, $aMapping)
    {
        $aCollection = array();

        if (!empty($aRows)) {

            foreach ($aRows as $aRow) {

                $aCollection[] = EntityFactory::newInstance($sEntityName, $aRow, $aMapping);
            }
        }

        return $aCollection;
    }

    /**
     * Flush will synchronise all entities in the unit of work with the database.
     * 
     * Unit of work is cleared after the flush.
     */
    public function flush()
    {
        //Flush all objects marked for insert, update and delete.
        //Retrieve each type from the unit of work and call the appropriate prepare statement object
        //Get the data object from dbal and pas to the appropriate instance of a prepared statement.
        //Call execute on PDO object.  Persist all as a transaction.
        $oDao = $this->oDbal->getDataObject();
        
        //Allow batch operations
        $aRegistry = $this->oUnitOfWork->getRegistry();
        $this->performInsert($oDao, $aRegistry);
        $this->performDelete($oDao, $aRegistry);
        
        $this->oUnitOfWork->clear();
    }

    /**
     * Execute all PDO insert statements against entities stored in the unit of work marked as new.
     * 
     * @param \PDO $oPDO
     * @param array $aRegistry
     * @throws OrmException
     */
    private function performInsert(\PDO $oPDO, array $aRegistry)
    {
        $aNew = $aRegistry[UnitOfWork::ENTITY_NEW];
        foreach ($aNew as $oEntity) {

            $oPDO->beginTransaction();
            //Will return complete statement for Joins.
            //Constraints and joins are not implemented yet.
            $oStatment = $this->oPrepare->insert($oEntity, $this->oMapper, $oPDO);

            try
            {

                $oStatment->execute();
                $this->setIdForLastInsert($oPDO, $oEntity);
                $oPDO->commit();
                //Assign the last insert id to the entity here, use reflection to
                //add the value to the class attribute that maps to the db table primary key.
            } catch (\PDOException $oExp)
            {

                $oPDO->rollBack();
                throw OrmException::pdoException($oExp->getMessage(), $oExp);
            }
        }
    }

    /**
     * Fetches the last insert ID and assigns it to the persisted entity.
     * 
     * @param \PDO $oPDO
     * @param object $oEntity
     */
    private function setIdForLastInsert(\PDO $oPDO, $oEntity)
    {
        $oReflect = new \ReflectionClass(\get_class($oEntity));
        $aMapping = $this->oMapper->getMappingForEntity($oEntity);

        $sIdAttrName = $aMapping[EntityMapping::KEY_PRIMARY_ID][EntityMapping::ATTR_PROPERTY];

        $oProperty = $oReflect->getProperty($sIdAttrName);
        $oProperty->setAccessible(true);
        $oProperty->setValue($oEntity, $oPDO->lastInsertId());

        $this->mLastInsertID = $oPDO->lastInsertId();
    }

    /**
     * Execute all PDO dlete statements against entites stored in the unit of work marked as removed.
     * 
     * @param \PDO $oPDO
     * @param array $aRegistry
     * @throws OrmException
     */
    private function performDelete(\PDO $oPDO, array $aRegistry)
    {
        $aRemoved = $aRegistry[UnitOfWork::ENTITY_REMOVED];

        foreach ($aRemoved as $oEntity) {

            $oStatment = $this->oPrepare->delete($oEntity, $this->oMapper, $oPDO);
            $oPDO->beginTransaction();
            try
            {

                $oStatment->execute();
                $oPDO->commit();
                //Assign the last insertt id to the entity here, use reflection to 
                //add the value to the class attribute that maps to the db table primary key.
            } catch (\PDOException $oExp)
            {

                $oPDO->rollBack();
                //Capture the PDO exception, inject into an ORM exception and throw to the client.
                throw OrmException::pdoException($oExp->getMessage(), $oExp);
            }
        }
    }

    /**
     * Returns the primary id value for the last entity that was successfully persisted to the data layer.
     * 
     * @return int
     */
    public function getLastInsertId()
    {
        return $this->mLastInsertID;
    }

    /**
     * Returns an instance of the database abstraction layer manager.
     * 
     * @return DbalManager
     */
    public function getDbalManager()
    {
        return $this->oDbal;
    }
}