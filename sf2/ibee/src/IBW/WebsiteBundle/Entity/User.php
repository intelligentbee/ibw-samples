<?php

namespace IBW\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * IBW\WebsiteBundle\Entity\User
 */
class User implements UserInterface
{
    /**
     * @var \DateTime $created_at
     */
    private $created_at;
    
    /**
     * @var string $email
     */
    private $email;
  
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $gcm_devices;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $owned_teams
     */
    private $owned_teams;

    /**
     * @var string $password
     */
    private $password;
    
    /**
     * @var string $salt
     */
    private $salt;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $stairs_activities
     */
    private $stairs_activities;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $teams
     */
    private $teams;
    
    /**
     * @var \DateTime $updated_at
     */
    private $updated_at;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $user_teams
     */
    private $user_teams;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->gcm_devices = new \Doctrine\Common\Collections\ArrayCollection();  
        $this->owned_teams = new \Doctrine\Common\Collections\ArrayCollection();
        $this->stairs_activities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->teams = new \Doctrine\Common\Collections\ArrayCollection();
        $this->user_teams = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Returns the properties to be serialized
     *
     * @return array
     */
    public function __sleep()
    {
        return array('id');
    }
    
    /**
     * Returns a string representation of the user object.
     *
     * @return string The email of the user
     */
    public function __toString() {
        return $this->getEmail();
    }
    
    /**
     * Add gcm_devices
     *
     * @param IBW\WebsiteBundle\Entity\GcmDevice $gcmDevices
     * @return User
     */
    public function addGcmDevice(\IBW\WebsiteBundle\Entity\GcmDevice $gcmDevices)
    {
        $this->gcm_devices[] = $gcmDevices;
    
        return $this;
    }
    
    /**
     * Add owned_teams
     *
     * @param IBW\WebsiteBundle\Entity\Team $ownedTeams
     * @return User
     */
    public function addOwnedTeam(\IBW\WebsiteBundle\Entity\Team $ownedTeams)
    {
        $this->owned_teams[] = $ownedTeams;
    
        return $this;
    }
    
    /**
     * Add stairs_activities
     *
     * @param IBW\WebsiteBundle\Entity\StairsActivity $stairsActivities
     * @return User
     */
    public function addStairsActivitie(\IBW\WebsiteBundle\Entity\StairsActivity $stairsActivities)
    {
        $this->stairs_activities[] = $stairsActivities;
    
        return $this;
    }
    
    /**
     * Add user_teams
     *
     * @param IBW\WebsiteBundle\Entity\UserTeam $userTeams
     * @return User
     */
    public function addUserTeam(\IBW\WebsiteBundle\Entity\UserTeam $userTeams)
    {
        $this->user_teams[] = $userTeams;
    
        return $this;
    }
    
    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     *
     * @return void
     */
    public function eraseCredentials()
    {
        
    }
    
    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    
    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Get gcm_devices
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getGcmDevices()
    {
        return $this->gcm_devices;
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
    
    /*
     * Returns a name from email
     * 
     * @return string $parts[0]
     */
    public function getName()
    {
      $parts = preg_split("/[^a-zA-Z]+/", $this->getEmail(), -1, PREG_SPLIT_NO_EMPTY);
      
      return $parts[0];
    }
    
    /**
     * Get owned_teams
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getOwnedTeams()
    {
        return $this->owned_teams;
    }
    
    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * Returns the roles granted to the user.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        return array('ROLE_USER');
    }
    
    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string The salt
     */
    public function getSalt()
    {
        return $this->salt;
    }
    
    /**
     * Get stairs_activities
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getStairsActivities()
    {
        return $this->stairs_activities;
    }
    
    /**
     * Get the teams of which the user is part
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTeams()
    {
        // we have to do this beacuse loading an entity from the database does not call the constructor
        // where the $teams variable is set to new \Doctrine\Common\Collections\ArrayCollection()
        $this->teams = new \Doctrine\Common\Collections\ArrayCollection();
        foreach($this->getUserTeams() as $user_team) {
            $this->teams[] = $user_team->getTeam();
        }
        
        return $this->teams;
    }
    
    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
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
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }
    
    /**
     * Remove gcm_devices
     *
     * @param IBW\WebsiteBundle\Entity\GcmDevice $gcmDevices
     */
    public function removeGcmDevice(\IBW\WebsiteBundle\Entity\GcmDevice $gcmDevices)
    {
        $this->gcm_devices->removeElement($gcmDevices);
    }
    
    /**
     * Remove owned_teams
     *
     * @param IBW\WebsiteBundle\Entity\Team $ownedTeams
     */
    public function removeOwnedTeam(\IBW\WebsiteBundle\Entity\Team $ownedTeams)
    {
        $this->owned_teams->removeElement($ownedTeams);
    }
    
    /**
     * Remove stairs_activities
     *
     * @param IBW\WebsiteBundle\Entity\StairsActivity $stairsActivities
     */
    public function removeStairsActivitie(\IBW\WebsiteBundle\Entity\StairsActivity $stairsActivities)
    {
        $this->stairs_activities->removeElement($stairsActivities);
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }
    
    /** 
     * @ORM\PrePersist
     * 
     * @return User
     */
    public function setCreatedAtValue()
    {
        if(!$this->created_at)
        {
            $this->setCreatedAt(new \DateTime);
        }
        
        return $this;
    }
    
    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }
    
    /**
     * Set id
     *
     * @param integer $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    
        return $this;
    }

    /**
     * @ORM\PreUpdate
     * 
     * @return User
     */
    public function setUpdatedAtValue()
    {
        $this->setUpdatedAt(new \DateTime);
        
        return $this;
    }
}