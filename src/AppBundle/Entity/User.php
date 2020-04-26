<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use EventBundle\Entity\Event;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\ManyToMany(targetEntity="EventBundle\Entity\Event",mappedBy="participants")
     */
    private $evenements;

    /**
     * @ORM\OneToMany(targetEntity="EventBundle\Entity\Event", mappedBy="user")
     */
    private $myEventList;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getEvenements()
    {
        return $this->evenements;
    }

    /**
     * @param mixed $evenements
     */
    public function setEvenements($evenements)
    {
        $this->evenements = $evenements;
    }
    public function addEvenements(Event $evenements)
    {
        if (!$this->evenements->contains($evenements)) {
            $this->evenements[] = $evenements;
        }

        return $this;
    }

    public function removeEvenements(Event $evenements)
    {
        if ($this->evenements->contains($evenements)) {
            $this->evenements->removeElement($evenements);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMyEventList()
    {
        return $this->myEventList;
    }

    /**
     * @param mixed $myEventList
     */
    public function setMyEventList($myEventList)
    {
        $this->myEventList = $myEventList;
    }


}
