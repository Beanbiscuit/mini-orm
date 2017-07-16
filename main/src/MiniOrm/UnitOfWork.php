<?php

namespace MiniOrm;

use MiniOrm\OrmException;

/**
 * Unit of work for domain objects that are to be synced to a database.
 *
 * The unit of work also implements the SPL Countable interface.
 * 
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class UnitOfWork implements \Countable
{

    /**
     * Unit of work can be instantiated as a singleton.
     */
    use ObjectRegistry;

    /**
     * Array key for the new entity array.
     */
    const ENTITY_NEW = "ENTITY_NEW ";

    /**
     * Array key for the clean entity array.
     */
    const ENTITY_CLEAN = "ENTITY_CLEAN";

    /**
     *  Array key for the dirty entity array.
     */
    const ENTITY_DIRTY = "ENTITY_DIRTY";

    /**
     *  Array key for the remove entity array.
     */
    const ENTITY_REMOVED = "ENTITY_REMOVED";

    /**
     * The entity registry.
     * 
     * The entity manager will parse this array and perform the relevant operation on the entites.
     * 
     * Defining the array keys upon instantiation so client code wont need to check for the existence of each array key.
     * @var array
     */
    private $aEntityRegistry = array(
        self::ENTITY_NEW => array(),
        self::ENTITY_CLEAN => array(),
        self::ENTITY_REMOVED => array(),
        self::ENTITY_DIRTY => array()
    );

    /**
     * Mark an entity for an insert.
     * 
     * @param object $oEntity
     */
    public function registerNew($oEntity)
    {

        $sUniqueId = $this->getEntityHash($oEntity);
        $this->aEntityRegistry[self:: ENTITY_NEW][$sUniqueId] = $oEntity;
    }

    /**
     * Mark an entity for an update.
     * 
     * @param object $oEntity
     */
    public function registerDirty($oEntity)
    {

        $sUniqueId = $this->getEntityHash($oEntity);
        $this->aEntityRegistry[self:: ENTITY_DIRTY][$sUniqueId] = $oEntity;
    }

    /**
     * Mark an entity as unchanged and clean.
     * 
     * @param object $oEntity
     */
    public function registerClean($oEntity)
    {

        $sUniqueId = $this->getEntityHash($oEntity);
        $this->aEntityRegistry[self:: ENTITY_CLEANY][$sUniqueId] = $oEntity;
    }

    /**
     * Mark an entity for deletion for the database.
     * 
     * @param object $oEntity
     */
    public function registerRemoved($oEntity)
    {

        $sUniqueId = $this->getEntityHash($oEntity);
        $this->aEntityRegistry[self:: ENTITY_REMOVED][$sUniqueId] = $oEntity;
    }

    /**
     * Returns unit of work object registry array.
     * 
     * @return array
     */
    public function getRegistry()
    {

        return $this->aEntityRegistry;
    }

    /**
     * Clear the unit of work domain object registry.
     */
    public function clear()
    {

        $this->aEntityRegistry = array(
            self::ENTITY_NEW => array(),
            self::ENTITY_CLEAN => array(),
            self::ENTITY_REMOVED => array(),
            self::ENTITY_DIRTY => array()
        );
    }

    /**
     * Returns the totla number of objects held in the unit of work.
     * 
     * @return int
     */
    public function count()
    {

        $iNew = count($this->aEntityRegistry[self::ENTITY_NEW]);
        $iClean = count($this->aEntityRegistry[self::ENTITY_CLEAN]);
        $iRemoved = count($this->aEntityRegistry[self::ENTITY_REMOVED]);
        $iDirty = count($this->aEntityRegistry[self::ENTITY_DIRTY]);

        return $iNew + $iClean + $iRemoved + $iDirty;
    }

    /**
     * Returns a unique hash for the provided entity.
     * 
     * @param object $oEntity
     * @throws ORMException
     */
    private function getEntityHash($oEntity)
    {

        if (!is_null($oEntity)) {

            return \spl_object_hash($oEntity);
        } else {

            throw new OrmException('Entity cannot be null when passed to the unit of work.');
        }
    }

}
