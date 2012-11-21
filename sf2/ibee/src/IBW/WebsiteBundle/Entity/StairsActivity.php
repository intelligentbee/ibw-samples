<?php

namespace IBW\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IBW\WebsiteBundle\Entity\StairsActivity
 */
class StairsActivity
{
    /**
     * @var integer $amount
     */
    private $amount;

    /**
     * @var \DateTime $created_at
     */
    private $created_at;
    
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var boolean $is_deleted
     */
    private $is_deleted = false;
    
    /**
     * @var boolean $is_notification_sent
     */
    private $is_notification_sent = false;
    
    /**
     * @var float $lat
     */
    private $lat;

    /**
     * @var float $lng
     */
    private $lng;

    /**
     * @var IBW\WebsiteBundle\Entity\User
     */
    private $user;
    
    /**
     * Get amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get is_deleted
     *
     * @return boolean 
     */
    public function getIsDeleted()
    {
        return $this->is_deleted;
    }

    /**
     * Get is_notification_sent
     *
     * @return boolean 
     */
    public function getIsNotificationSent()
    {
        return $this->is_notification_sent;
    }

    /**
     * Get lat
     *
     * @return float 
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Get lng
     *
     * @return float 
     */
    public function getLng()
    {
        return $this->lng;
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
     * Set amount
     *
     * @param integer $amount
     * @return StairsActivity
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    
        return $this;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return StairsActivity
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }
    
    /**
     * @ORM\PrePersist
     * 
     * @return StairsActivity
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
     * Set is_deleted
     *
     * @param boolean $isDeleted
     * @return StairsActivity
     */
    public function setIsDeleted($isDeleted)
    {
        $this->is_deleted = $isDeleted;
    
        return $this;
    }

    /**
     * Set is_notification_sent
     *
     * @param boolean $isNotificationSent
     * @return StairsActivity
     */
    public function setIsNotificationSent($isNotificationSent)
    {
        $this->is_notification_sent = $isNotificationSent;
    
        return $this;
    }

    /**
     * Set lat
     *
     * @param float $lat
     * @return StairsActivity
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    
        return $this;
    }
    
    /**
     * Set lng
     *
     * @param float $lng
     * @return StairsActivity
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    
        return $this;
    }

    /**
     * Set user
     *
     * @param IBW\WebsiteBundle\Entity\User $user
     * @return StairsActivity
     */
    public function setUser(\IBW\WebsiteBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }
    
    /**
     * Set id
     *
     * @param integer $id
     * @return StairsActivity
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
    }
}