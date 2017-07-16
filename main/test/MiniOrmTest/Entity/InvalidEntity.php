<?php

namespace MiniOrmTest\Entity;

/**
 * Plain ol PHP object used for testing the ORM package.
 *
 * @author Bruce Silver <beanbiscuit@gmail.com>
 */
class InvalidEntity
{

    /**
     *
     * @var int
     */
    private $id;

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var string
     */
    private $code;

    function getId()
    {
        return $this->id;
    }

    function getName()
    {
        return $this->name;
    }

    function getCode()
    {
        return $this->code;
    }

    function setId($id)
    {
        $this->id = $id;
    }

    function setName($name)
    {
        $this->name = $name;
    }

    function setCode($code)
    {
        $this->code = $code;
    }

}
