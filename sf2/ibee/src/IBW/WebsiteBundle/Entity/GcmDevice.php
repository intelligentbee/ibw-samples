<?php

namespace IBW\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IBW\WebsiteBundle\Entity\GcmDevice
 */
class GcmDevice
{
    /**
     * @var \DateTime $created_at
     */
    private $created_at;
  
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $reg_id
     */
    private $reg_id;
    
    /**
     * @var \DateTime $updated_at
     */
    private $updated_at;

    /**
     * @var IBW\WebsiteBundle\Entity\User
     */
    private $user;
    
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Get reg_id
     *
     * @return string 
     */
    public function getRegId()
    {
        return $this->reg_id;
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
     * Get user
     *
     * @return IBW\WebsiteBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return GcmDevice
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }
    

    /**
     * @ORM\PrePersist
     * 
     * @return GcmDevice
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
     * Set reg_id
     *
     * @param string $regId
     * @return GcmDevice
     */
    public function setRegId($regId)
    {
        $this->reg_id = $regId;
    
        return $this;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return GcmDevice
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    
        return $this;
    }

    /**
     * @ORM\PrePersist
     * 
     * @return GcmDevice
     */
    public function setUpdatedAtValue()
    {
        $this->setUpdatedAt(new \DateTime);
        
        return $this;
    }

    /**
     * Set user
     *
     * @param IBW\WebsiteBundle\Entity\User $user
     * @return GcmDevice
     */
    public function setUser(\IBW\WebsiteBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }
}