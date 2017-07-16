<?php
namespace MiniOrm\Prepare;

use MiniOrm\Mapping\EntityMapping;

/**
 * The prepare statement interface.
 * 
 * Allows the implementation of preparing PDO statements to be abstracted.
 * 
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
interface PrepareStatement
{

    /**
     * 
     */
    const INSERT_STATEMENT = 'INSERT INTO';

    /**
     * 
     */
    const UPDATE_STATEMENT = 'UPDATE';

    /**
     * 
     */
    const DELETE_STATEMENT = 'DELETE FROM';

    /**
     * Build a PDO insert statement for the given entity. 
     * 
     * @param string $oEntity - The entity to persist.
     * @param EntityMapping $oMapping - Property to db column mappings for the entity.
     * @param \PDO $sPDO - The data access object.
     */
    public function insert($oEntity, EntityMapping $oMapping, \PDO $sPDO);

    /**
     * Builds am SQL delete statement for the given entity and mappings.
     *
     * @param object $oEntity
     * @param EntityMapping $oMapping
     * @param \PDO $sPDO
     */
    public function delete($oEntity, EntityMapping $oMapping, \PDO $sPDO);
}
