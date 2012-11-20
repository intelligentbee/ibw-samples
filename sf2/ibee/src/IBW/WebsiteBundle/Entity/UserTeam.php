<?php

namespace IBW\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IBW\WebsiteBundle\Entity\UserTeam
 */
class UserTeam
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var IBW\WebsiteBundle\Entity\Team
     */
    private $team;
    
    /**
     * @var IBW\WebsiteBundle\Entity\User
     */
    private $user;

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
     * Get team
     *
     * @return IBW\WebsiteBundle\Entity\Team 
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Get user
     *
     * @return IBW\WebsiteBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set team
     *
     * @param IBW\WebsiteBundle\Entity\Team $team
     * @return UserTeam
     */
    public function setTeam(\IBW\WebsiteBundle\Entity\Team $team = null)
    {
        $this->team = $team;
    
        return $this;
    }

    /**
     * Set user
     *
     * @param IBW\WebsiteBundle\Entity\User $user
     * @return UserTeam
     */
    public function setUser(\IBW\WebsiteBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }
    
    /**
     * Set id
     *
     * @param integer Id
     * @return UserTeam
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
    }
}