<?php
namespace MiniOrm\Entity;

/**
 * POPO for testing the mini orm framework.
 * 
 * @Entity
 * @Table(name='application_user')
 */
class ApplicationUser
{

    /**
     * @var integer
     * 
     * @Id(name='id', type='int')
     */
    private $id;

    /**
     * @var string
     * 
     * @Column(name='username', type='string')
     */
    private $username;

    /**
     * @Column(name='email',  type='string', length='255')
     */
    private $email;

    /**
     * @var string
     * 
     * @Column(name='display_name', type='string', length='255')
     */
    private $displayName;

    /**
     * @var \DateTime
     * 
     * @Column(name='created_on', type='datetime')
     */
    private $createdOn;

    function getId()
    {
        return $this->id;
    }

    function getUsername()
    {
        return $this->username;
    }

    function getEmail()
    {
        return $this->email;
    }

    function getDisplayName()
    {
        return $this->displayName;
    }

    function getCreatedOn()
    {
        return $this->createdOn;
    }

    function setId($id)
    {
        $this->id = $id;
    }

    function setUsername($username)
    {
        $this->username = $username;
    }

    function setEmail($email)
    {
        $this->email = $email;
    }

    function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    function setCreatedOn(\DateTime $createdOn)
    {
        $this->createdOn = $createdOn;
    }

}
