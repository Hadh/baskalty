<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use EventBundle\Entity\Event;
use EventBundle\Entity\Participation;
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
     * @ORM\OneToMany(targetEntity="EventBundle\Entity\Participation", mappedBy="participant")
     * */
    protected $participation;

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
    public function getParticipation()
    {
        return $this->participation;
    }

    /**
     * @param mixed $participation
     */
    public function setParticipation($participation)
    {
        $this->participation = $participation;
    }
    public function addParticipation(Participation $participation)
    {
        if (!$this->participation->contains($participation)) {
            $this->participation[] = $participation;
        }

        return $this;
    }

    public function removeParticipation(Participation $participation)
    {
        if ($this->participation->contains($participation)) {
            $this->participation->removeElement($participation);
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
