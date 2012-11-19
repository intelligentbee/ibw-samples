<?php

namespace IBW\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IBW\WebsiteBundle\Entity\Team
 */
class Team
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var IBW\WebsiteBundle\Entity\User
     */
    private $owner;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $user_teams;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $users
     */
    private $users;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user_teams = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add user_teams
     *
     * @param IBW\WebsiteBundle\Entity\UserTeam $userTeams
     * @return Team
     */
    public function addUserTeam(\IBW\WebsiteBundle\Entity\UserTeam $userTeams)
    {
        $this->user_teams[] = $userTeams;
    
        return $this;
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get owner
     *
     * @return IBW\WebsiteBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }
    
    /**
     * Get user_teams
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUserTeams()
    {
        return $this->user_teams;
    }
    
    /**
     * Get the users that are part of this team
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        // we have to do this beacuse loading an entity from the database does not call the constructor
        // where the $users variable is set to new \Doctrine\Common\Collections\ArrayCollection()
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        foreach($this->getUserTeams() as $user_team) {
            $this->users[] = $user_team->getUser();
        }

        return $this->users;
    }
    
    /*
     * Checks if a user belongs to this team
     * 
     * @param \IBW\WebsiteBundle\Entity\User  $user  The user that will be checked if belongs to this team
     * @return bool Returns true if the user belongs to this team, false othewise
     */
    public function hasUser(\IBW\WebsiteBundle\Entity\User $user)
    {
        foreach($this->getUserTeams() as $user_team) {
          if($user_team->getUser()->getId() == $user->getId()) {
            return true;
          }
        }
        
        return false;
    }
    
    /**
     * Remove user_teams
     *
     * @param IBW\WebsiteBundle\Entity\UserTeam $userTeams
     */
    public function removeUserTeam(\IBW\WebsiteBundle\Entity\UserTeam $userTeams)
    {
        $this->user_teams->removeElement($userTeams);
    }
    
    /**
     * Set id
     *
     * @param integer $id
     * @return Team
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return Team
     */
    public function setName($name)
    {
        $this->name = trim($name);
    
        return $this;
    }

    /**
     * Set owner
     *
     * @param IBW\WebsiteBundle\Entity\User $owner
     * @return Team
     */
    public function setOwner(\IBW\WebsiteBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;
    
        return $this;
    }
}